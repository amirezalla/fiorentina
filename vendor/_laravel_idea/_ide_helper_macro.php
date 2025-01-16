<?php //3871e771d304e0d4ba609d26f7a0c46d
/** @noinspection all */

namespace Illuminate\Database\Eloquent {

    use Illuminate\Support\HigherOrderTapProxy;
    
    /**
     * @method $this|Model|HigherOrderTapProxy|mixed createOrRestore(array $attributes = [], array $values = [])
     * @method void downloadExcel(string $writerType = null, $withHeadings = false)
     * @method void import(string $disk = null, string $readerType = null)
     * @method void importAs(callable $mapping, string $disk = null, string $readerType = null)
     * @method $this onlyTrashed()
     * @method int restore()
     * @method $this|Model|HigherOrderTapProxy|mixed restoreOrCreate(array $attributes = [], array $values = [])
     * @method void storeExcel(string $disk = null, string $writerType = null, $withHeadings = false)
     * @method $this withTrashed($withTrashed = true)
     * @method $this withoutTrashed()
     */
    class Builder {}
}

namespace Illuminate\Http {
    
    /**
     * @method bool hasValidRelativeSignature()
     * @method bool hasValidSignature($absolute = true)
     * @method bool hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
     * @method array validate(array $rules, ...$params)
     * @method void validateWithBag(string $errorBag, array $rules, ...$params)
     */
    class Request {}
}

namespace Illuminate\Routing {
    
    /**
     * @method $this wherePrimaryKey(array|null|string $name = 'id')
     */
    class Route {}
}

namespace Illuminate\Support {
    
    /**
     * @method $this debug()
     * @method void downloadExcel(string $fileName, string $writerType = null, $withHeadings = false, array $responseHeaders = [])
     * @method void storeExcel(string $filePath, string $disk = null, string $writerType = null, $withHeadings = false)
     */
    class Collection {}
}

namespace Illuminate\Validation {

    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Foundation\Application as Application1;
    use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;
    
    /**
     * @method Application|Application1|mixed|Turnstile turnstile()
     */
    class Rule {}
}

namespace Kris\LaravelFormBuilder\Supports {

    use Illuminate\Support\HtmlString;
    
    /**
     * @method HtmlString|void customLabel($name, $value, $options = [], $escapeHtml = true)
     */
    class FormBuilder {}
}