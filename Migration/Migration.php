<?php


namespace Migration;


use Cli\Cli;
use Credentials\Credentials;
use Helper\CredentialHelper;
use Helper\SqlHelper;
use Neoan3\Apps\Ops;

class Migration
{
    private Cli $cli;
    public ?string $direction = null;
    private SqlHelper $SqlHelper;
    public CredentialHelper $credentialHelper;
    public Credentials $credentials;
    public array $knownModels;
    public array $knownTables;
    public DataBase $db;

    function __construct(Cli $cli, DataBase $Db)
    {
        $this->cli = $cli;
        $this->credentials = new Credentials($cli);
        $this->db = $Db;
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
        $format = [
            'host' => 'localhost',
            'name' => 'neoan3_db',
            'user' => 'root',
            'password' => '',
            'port' => 3306,
            'assumes_uuid' => 'true'
        ];
        $this->credentials->chooseCredentials($format);
        $this->SqlHelper = new SqlHelper($this->credentials->currentCredentials, $this->db);
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
                $constraints = [];
                if (isset($this->knownTables[$targetTable])) {
                    $sql = "ALTER TABLE `$targetTable`\n";
                    foreach ($columns as $columnKey => $columnValue) {
                        if (isset($this->knownTables[$targetTable][$columnKey])) {
                            $this->dropKey($targetTable, $columnKey, $columnValue);
                            $sql .= "\tMODIFY " . $this->sqlRow($columnKey, $columnValue);
                        } else {
                            $sql .= "\tADD COLUMN " . $this->sqlRow($columnKey, $columnValue);
                        }
                        $constraints[] = [$targetTable, $columnKey, $columnValue];
                    }
                    $sql = substr($sql, 0, -2) . "\n";
                } else {

                    $sql = "CREATE TABLE `$targetTable`(\n";
                    foreach ($columns as $columnKey => $columnValue) {
                        $sql .= $this->sqlRow($columnKey, $columnValue);
                        if($columnValue['key']){
                            $constraints[] = [$targetTable, $columnKey, $columnValue];
                        }
                    }
                    $sql = substr($sql, 0, -2);
                    $sql .= "\n)";
                }
                try {
                    $this->db->query('>' . $sql);
                    foreach ($constraints as $constraint){
                        $this->addKey(...$constraint);
                    }

                } catch (\Exception $e) {
                    $this->cli->printLn('SQL Error: ' . $e->getMessage(), 'red');
                    $this->cli->printLn('Executed: ' . $sql, 'red');
                }
            }
        }
    }
    function dropKey($table, $column, $value){
        if($value['key']){
            $keyMatch = [
                'primary'=>'PRIMARY KEY', 'unique'=>"KEY $column", true => "KEY $column"
            ];
            $sql = ">ALTER TABLE `$table` DROP " . $keyMatch[$value['key']];
            try {
                $this->db->query($sql);
            } catch (\Exception $e) {
                $this->cli->printLn("SQL Warning: " . $e->getMessage());
            }
        }
    }
    function addKey($table, $column, $value){
        if($value['key']){
            $keyMatch = [
                'primary'=>"PRIMARY KEY(`$column`)",
                'unique'=>"CONSTRAINT `$column` UNIQUE (`$column`)",
                true => "KEY (`$column`)"];
            $sql = ">ALTER TABLE `$table` ADD " . $keyMatch[$value['key']];
            try {
                $this->db->query($sql);
            } catch (\Exception $e) {
                $this->cli->printLn("NOTICE: " . $e->getMessage() . "\nThis is likely not an issue.");
            }
        }
    }

    function sqlRow($columnKey, $columnValue): string
    {
        $sql = "\t`$columnKey`\t{$columnValue['type']}\t";
        $sql .= $columnValue['default'] ? 'default ' . $this->defaultQuotes($columnValue['type'], $columnValue['default']) : '';
        $sql .= "\t" . (!$columnValue['nullable'] ? 'not null' : 'null');
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
            $opsMethod = $this->cli->versionHelper->casing;
            $folder = Ops::$opsMethod($modelKey);
            file_put_contents($this->cli->workPath . "/model/$folder/migrate.json", json_encode($migrate));
        }
    }

    function getKnownModels()
    {
        $dir = $this->cli->workPath . '/model/';
        $files = scandir($dir);
        foreach ($files as $folder) {
            if (is_dir($dir . '/' . $folder) && $folder != 'index' && $folder !== '.' && $folder !== '..') {
                $this->knownModels[Ops::toSnakeCase($folder)] = json_decode(file_get_contents($dir . '/' . $folder . '/migrate.json'), true);
            }
        }
    }

    function getKnownTables()
    {
        $db = [];
        foreach ($this->SqlHelper->databaseTables() as $table) {
            echo "migrating $table\n";
            $db[$table] = [];
            foreach ($this->SqlHelper->describeTable($table) as $description) {
                $index = strtolower(substr($description['Key'],0,3));
                $keyMatch = ['pri'=>'primary', 'uni'=>'unique','mul' => true, '' => false];
                $key = $keyMatch[$index] ?? false;
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

    private function defaultQuotes($type, $value): string
    {
        if (preg_match('/enum|varchar|text|longtext|mediumtext/', $type) === 1) {
            return '"' . $value . '"';
        } else {
            return $value;
        }
    }

}