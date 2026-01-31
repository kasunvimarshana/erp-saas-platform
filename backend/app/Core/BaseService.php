<?php

namespace App\Core;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class BaseService
 * 
 * Base service class providing transaction management and common business logic patterns
 * 
 * @package App\Core
 */
abstract class BaseService
{
    /**
     * Execute a callback within a database transaction
     *
     * @param callable $callback
     * @return mixed
     * @throws Exception
     */
    protected function transaction(callable $callback): mixed
    {
        try {
            DB::beginTransaction();
            
            $result = $callback();
            
            DB::commit();
            
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Transaction failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Log service activity
     *
     * @param string $action
     * @param array $context
     * @return void
     */
    protected function logActivity(string $action, array $context = []): void
    {
        Log::info($action, array_merge([
            'service' => static::class,
            'timestamp' => now()->toDateTimeString()
        ], $context));
    }

    /**
     * Handle service exception
     *
     * @param Exception $e
     * @param string $message
     * @return void
     * @throws Exception
     */
    protected function handleException(Exception $e, string $message): void
    {
        Log::error($message, [
            'exception' => $e->getMessage(),
            'service' => static::class,
            'trace' => $e->getTraceAsString()
        ]);
        
        throw new Exception($message . ': ' . $e->getMessage(), 0, $e);
    }

    /**
     * Validate required data fields
     *
     * @param array $data
     * @param array $required
     * @return void
     * @throws Exception
     */
    protected function validateRequired(array $data, array $required): void
    {
        $missing = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            throw new Exception('Missing required fields: ' . implode(', ', $missing));
        }
    }
}
