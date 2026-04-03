<?php

namespace Core\ActivityLog\Infrastructure\Helpers;

class AuditDiffBuilder
{
    protected array $oldData = [];
    protected array $newData = [];
    protected array $diff = [];
    protected array $ignored = [
        'created_at',
        'updated_at',
        'deleted_at',
        'diff'
    ];

    public function __construct(array|string $oldData = [], array|string $newData = [])
    {
        $this->oldData = $this->parse($oldData);
        $this->newData = $this->parse($newData);
    }

    protected function parse(array|string|null $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_string($data)) {
            return json_decode($data, true) ?? [];
        }

        return [];
    }

    public function ignore(array $fields): self
    {
        $this->ignored = array_merge($this->ignored, $fields);
        return $this;
    }

    public function compare(): self
    {
        $this->diff = $this->findDiff($this->oldData, $this->newData);
        return $this;
    }

    protected function findDiff(array $old, array $new): array
    {
        $diff = [];
        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($keys as $key) {
            if (in_array($key, $this->ignored)) {
                continue;
            }

            $oldValue = $old[$key] ?? null;
            $newValue = $new[$key] ?? null;

            if ($oldValue !== $newValue) {
                $diff[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $diff;
    }

    public function toArray(): array
    {
        return $this->diff;
    }

    public static function make(array|string $old, array|string $new): self
    {
        return new self($old, $new);
    }
}
