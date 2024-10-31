<?php //fab3075a55fb483311bed73b22d19beb
/** @noinspection all */

namespace Illuminate\Database\Eloquent {

    use Illuminate\Support\HigherOrderTapProxy;

    /**
     * @method static $this|Model|HigherOrderTapProxy|mixed createOrRestore(array $attributes = [], array $values = [])
     * @method static void downloadExcel(string $writerType = null, $withHeadings = false)
     * @method static void import(string $disk = null, string $readerType = null)
     * @method static void importAs(callable $mapping, string $disk = null, string $readerType = null)
     * @method static $this onlyTrashed()
     * @method static int restore()
     * @method static $this|Model|HigherOrderTapProxy|mixed restoreOrCreate(array $attributes = [], array $values = [])
     * @method static void storeExcel(string $disk = null, string $writerType = null, $withHeadings = false)
     * @method static $this withTrashed($withTrashed = true)
     * @method static $this withoutTrashed()
     */
    class Builder {}
}

namespace Illuminate\Http {

    /**
     * @method static bool hasValidRelativeSignature()
     * @method static bool hasValidSignature($absolute = true)
     * @method static bool hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
     * @method static array validate(array $rules, ...$params)
     * @method static void validateWithBag(string $errorBag, array $rules, ...$params)
     */
    class Request {}
}

namespace Illuminate\Routing {

    /**
     * @method static $this wherePrimaryKey(array|null|string $name = 'id')
     */
    class Route {}
}

namespace Illuminate\Support {

    /**
     * @method static $this debug()
     * @method static void downloadExcel(string $fileName, string $writerType = null, $withHeadings = false, array $responseHeaders = [])
     * @method static void storeExcel(string $filePath, string $disk = null, string $writerType = null, $withHeadings = false)
     */
    class Collection {}
}

namespace Kris\LaravelFormBuilder\Supports {

    use Illuminate\Support\HtmlString;

    /**
     * @method static HtmlString|void customLabel($name, $value, $options = [], $escapeHtml = true)
     */
    class FormBuilder {}
}
