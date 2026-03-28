<?php

namespace YourVendor\LaravelHx\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use YourVendor\LaravelHx\HxManager;

class HxExceptionHandler
{
    /**
     * HTMX管理器实例
     */
    protected HxManager $hx;

    public function __construct(HxManager $hx)
    {
        $this->hx = $hx;
    }

    /**
     * 处理异常
     */
    public function handle(Exception $exception, Request $request)
    {
        if (!$this->hx->isHtmxRequest()) {
            return null; // 让Laravel默认处理器处理
        }

        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($exception, $request);
        }

        return $this->handleGeneralException($exception, $request);
    }

    /**
     * 处理验证异常
     */
    protected function handleValidationException(ValidationException $exception, Request $request)
    {
        $errors = $exception->errors();
        
        // 渲染错误视图
        $errorView = view('hx.partials.error', ['errors' => $exception->validator])
            ->render();
        
        // 返回422状态码和错误HTML
        return response($errorView, 422)
            ->header('HX-Retarget', '#hx-errors')
            ->header('HX-Reswap', 'innerHTML');
    }

    /**
     * 处理一般异常
     */
    protected function handleGeneralException(Exception $exception, Request $request)
    {
        $errorMessage = config('app.debug') ? $exception->getMessage() : 'An error occurred';
        
        $errorHtml = "<div class='alert alert-danger'>{$errorMessage}</div>";
        
        return response($errorHtml, 500)
            ->header('HX-Retarget', '#hx-errors')
            ->header('HX-Reswap', 'innerHTML');
    }

    /**
     * 渲染验证错误到指定目标
     */
    public function renderValidationErrors(array $errors, string $target = '#hx-errors'): string
    {
        $errorList = '';
        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $errorList .= "<li>{$message}</li>";
            }
        }
        
        return "
            <div id='hx-errors' class='alert alert-danger'>
                <ul class='mb-0'>{$errorList}</ul>
            </div>
        ";
    }
}