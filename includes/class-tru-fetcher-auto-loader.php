<?php

class Tru_Fetcher_Auto_Loader
{

    const APP_NAME = 'TruFetcher';

    private string $className;
    private string $classFilenamePrefix = 'class';
    private string $classFilenameExt = 'php';
    private array $conversionTypes = ["default", "to_hyphens", "to_underscores"];

    public function __construct()
    {

    }

    public function init(string $className)
    {
        if (strpos($className, self::APP_NAME) === false) {
            return;
        }

        $this->className = $className;
        $this->loadClass();
    }

    private function loadClass()
    {
        if (class_exists($this->className)) {
            return;
        }

        foreach ($this->conversionTypes as $type) {
            $path = $this->convertClassnameToFilePath($type);
            if (file_exists($path)) {
                require_once($path);
                return;
            }
        }
    }

    private function convertClassnameToArray(string $conversionType) {
        $buildClassnameArray = [];
        $classToArray = explode("\\", $this->className);
        $last = array_key_last($classToArray);
        foreach ($classToArray as $key => $val) {
            if ($val === self::APP_NAME) {
                continue;
            }
            if ($key !== $last) {
                switch ($conversionType) {
                    case "to_hyphens":
                        $val = self::camelCaseToSnakeCase($val, '-');
                        break;
                    case "to_underscores":
                        $val = self::camelCaseToSnakeCase($val);
                        break;
                }
            }
            $buildClassnameArray[] = strtolower($val);
        }
        return $buildClassnameArray;
    }

    private function convertClassnameToFilePath(string $conversionType)
    {
        $classToArray = $this->convertClassnameToArray($conversionType);
        $extractClassname = $classToArray[array_key_last($classToArray)];
        $filename = $this->convertClassnameToFilename($extractClassname);
        $classToArray[array_key_last($classToArray)] = $filename;
        return TRU_FETCHER_PLUGIN_DIR . implode('/', $classToArray);
    }

    private function convertClassnameToFilename(string $classname)
    {
        $classnameToArray = array_map("strtolower", explode("_", $classname));
        return sprintf(
            '%s-%s.%s',
            $this->classFilenamePrefix,
            implode('-', $classnameToArray),
            $this->classFilenameExt
        );
    }

    public static function camelCaseToSnakeCase(string $input, ?string $separator = "_")
    {
        return preg_replace('/(?<!^)[A-Z]/', "$separator$0", $input);
    }

}
