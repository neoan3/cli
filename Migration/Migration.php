<?php


namespace Migration;


use Helper\CredentialHelper;
use Helper\SqlHelper;
use Neoan3\Apps\Db;
use Neoan3\Apps\DbException;

class Migration
{
    private \Cli\Cli $cli;
    public ?string $direction = null;
    private array $usedCredentials;
    private SqlHelper $SqlHelper;
    public CredentialHelper $credentialHelper;
    public array $knownModels;
    public array $knownTables;

    function __construct(\Cli\Cli $cli)
    {
        $this->cli = $cli;
        $this->credentialHelper = new CredentialHelper($cli);
        $this->process();
    }

    function checkProperFormat()
    {
        $ok = true;
        if (!isset($this->cli->arguments[2])) {
            $ok = false;
        } elseif (in_array($this->cli->arguments[2], ['up', 'down'])) {
            $this->direction = $this->cli->arguments[2];
        }
        if (!$this->direction && !isset($this->cli->arguments[3])) {

            $ok = false;
        } elseif (isset($this->cli->arguments[3]) && in_array($this->cli->arguments[3], ['up', 'down'])) {
            $this->direction = $this->cli->arguments[3];
        } elseif (!$this->direction) {
            $ok = false;
        }
        if (!$ok) {
            $this->cli->printLn('Malformed command', 'red');
            $this->cli->printLn('Examples:', 'yellow');
            $this->cli->printLn('"neoan3 migrate models up"', 'yellow');
            $this->cli->printLn('"neoan3 migrate models down"', 'yellow');
        }
        return $ok;
    }

    function chooseCredentials()
    {
        $credentials = $this->credentialHelper->readCredentials();
        $i = 0;
        $this->cli->printLn('Choose credentials', 'green');
        foreach ($credentials as $key => $credential) {
            $this->cli->printLn('[' . $i . '] ' . $key, 'green');
            $i++;
        }
        $this->cli->printLn('[x] create new credentials', 'yellow');
        $this->cli->waitForSingleInput(function ($input) use ($credentials) {
            if ($input === 'x') {
                $this->cli->printLn('');
                $this->credentialHelper->createNew([
                    'host' => 'localhost',
                    'name' => 'neoan3_db',
                    'user' => 'root',
                    'password' => '',
                    'port' => 3306,
                    'assumes_uuid' => 'true'
                ]);
                $this->usedCredentials = $this->credentialHelper->credentials[$this->credentialHelper->currentCredentialName];
            } else {
                $this->usedCredentials = $this->credentialHelper->credentials[array_keys($credentials)[$input]];
            }
        });
        $this->SqlHelper = new SqlHelper($this->usedCredentials);
    }

    function process()
    {
        if (!$this->checkProperFormat()) {
            return;
        }
        $this->chooseCredentials();
        $this->getKnownModels();
        $this->getKnownTables();
        $this->cli->printLn('migrating...', 'magenta');
        if ($this->direction === 'up') {
            $this->processUp();
        } else {
            $this->processDown();
        }
        $this->cli->printLn('done', 'magenta');

    }

    function processUp()
    {
        foreach ($this->knownModels as $modelKey => $knownModel) {
            foreach ($knownModel as $targetTable => $columns) {

                if (isset($this->knownTables[$targetTable])) {
                    $unique = '';
                    try {
                        Db::ask(">ALTER TABLE `$targetTable` DROP PRIMARY KEY");
                    } catch (DbException $e) {
                        $this->cli->printLn("SQL Error: " . $e->getMessage());
                    }
                    $sql = "ALTER TABLE `$targetTable`\n";
                    foreach ($columns as $columnKey => $columnValue) {

                        if (isset($this->knownTables[$targetTable][$columnKey])) {
                            $sql .= "\tMODIFY " . $this->sqlRow($columnKey, $columnValue);
                        } else {
                            $sql .= "\tADD COLUMN " . $this->sqlRow($columnKey, $columnValue);
                        }
                    }
                    $sql = substr($sql, 0, -2) . "\n";
                } else {
                    $unique = '';
                    $sql = "CREATE TABLE `$targetTable`(\n";
                    foreach ($columns as $columnKey => $columnValue) {
                        if ($columnValue['key'] === 'unique') {
                            $unique .= "\tUNIQUE(`$columnKey`),\n";
                        }
                        $sql .= $this->sqlRow($columnKey, $columnValue);
                    }
                    $sql .= $unique;
                    $sql = substr($sql, 0, -2);
                    $sql .= "\n)";
                }
                try {
                    Db::ask('>' . $sql);
                } catch (DbException $e) {
                    $this->cli->printLn('SQL Error: ' . $e->getMessage(), 'red');
                    $this->cli->printLn('Executed: ' . $sql, 'red');
                }

            }
        }
    }

    function sqlRow($columnKey, $columnValue)
    {
        $sql = "\t`$columnKey`\t{$columnValue['type']}\t";
        $sql .= $columnValue['default'] ? 'default \'' . $columnValue['default'] . '\'' : '';
        $sql .= "\t" . (!$columnValue['nullable'] ? 'not null' : 'null');
        $sql .= "\t" . ($columnValue['key'] === 'primary' ? 'primary key' : '');
        $sql .= ",\n";
        return $sql;
    }

    function processDown()
    {
        foreach ($this->knownModels as $modelKey => $knownModel) {
            $migrate = [];
            foreach ($this->knownTables as $tableKey => $knownTable) {
                if (strpos($tableKey, $modelKey) !== false) {
                    $migrate[$tableKey] = $knownTable;
                }
            }
            file_put_contents($this->cli->workPath . "/model/$modelKey/migrate.json", json_encode($migrate));
        }
    }

    function getKnownModels()
    {
        $dir = $this->cli->workPath . '/model/';
        $files = scandir($dir);
        foreach ($files as $folder) {
            if (is_dir($dir . '/' . $folder) && $folder != 'index' && $folder !== '.' && $folder !== '..') {
                $this->knownModels[$folder] = json_decode(file_get_contents($dir . '/' . $folder . '/migrate.json'), true);
            }
        }
    }

    function getKnownTables()
    {
        $db = [];
        foreach ($this->SqlHelper->databaseTables() as $table) {
            $db[$table] = [];
            foreach ($this->SqlHelper->describeTable($table) as $description) {
                $key = $description['Key'] === 'PRI' ? 'primary' : false;
                if ($description['Key'] === 'UNI') {
                    $key = 'unique';
                }
                $db[$table][$description['Field']] = [
                    'type' => $description['Type'],
                    'key' => $key,
                    'nullable' => $description['Null'] === 'NO' ? false : true,
                    'default' => $description['Default'] ? $description['Default'] : false,
                    'a_i' => $description['Extra'] === 'auto_increment',
                ];
            }

        }
        $this->knownTables = $db;
    }

}