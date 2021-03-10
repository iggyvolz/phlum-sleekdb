<?php


namespace iggyvolz\phlum\SleekDB;

use iggyvolz\phlum\Condition;
use iggyvolz\phlum\PhlumDriver;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SleekDB\Store;

class SleekDBDriver implements PhlumDriver
{
    public function __construct(
        private string $dataDir
    ) {
    }

    private function getTable(string $table): Store
    {
        return new Store($table, $this->dataDir);
    }

    /**
     * @param string $table
     * @param array<string, string|int|float|null> $data
     * @return int
     */
    public function create(string $table, array $data): int
    {
        return self::getTable($table)->insert($data)["_id"];
    }

    /**
     * @param string $table
     * @param int $id
     * @return array<string, string|int|float|null>
     */
    public function read(string $table, int $id): array
    {
        return self::getTable($table)->findById($id);
    }

    /**
     * @param string $table
     * @param array<string, Condition> $condition
     * @return list<int>
     */
    public function readMany(
        string $table,
        array $condition
    ): array
    {
        return iterator_to_array((function() use($table, $condition): \Generator{
            foreach(self::getTable($table)->findAll() as $entry) {
                $keep = true;
                foreach ($condition as $key => $conditionObject) {
                    if (!$conditionObject->check($entry[$key] ?? null)) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    yield $entry["_id"];
                }
            }
        })());
    }

    /**
     * @param string $table
     * @param int $id
     * @param array<string, string|int|float|null> $data
     */
    public function update(string $table, int $id, array $data): void
    {
        self::getTable($table)->updateById($id, $data);
    }
    /**
     * @param string $table
     * @param array<string, Condition> $condition
     * @param array<string, string|int|float|null> $data
     */
    public function updateMany(string $table, array $condition, array $data): void
    {
        foreach($this->readMany($table, $condition) as $id)
        {
            $this->update($table, $id, $data);
        }
    }

    /**
     * @param string $table
     * @param int $id
     */
    public function delete(string $table, int $id): void
    {
        self::getTable($table)->deleteById($id);
    }

    /**
     * @param string $table
     * @param array<string, Condition> $condition
     */
    public function deleteMany(string $table, array $condition): void
    {
        foreach($this->readMany($table, $condition) as $id)
        {
            $this->delete($table, $id);
        }
    }
}