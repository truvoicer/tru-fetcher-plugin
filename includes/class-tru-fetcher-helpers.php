<?php
namespace TruFetcher\Includes;

use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu_Constants;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://news-author.com
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael Truvoice <mikydxl@gmail.com>
 */
class Tru_Fetcher_Helpers {
    public static function toSnakeCase(string $text) {
        return str_replace('-', '_', $text);
    }
    public static function dump(...$vars) {
        var_dump(...$vars);
        wp_die();
    }

    public static function getConfigContents($configName = null, $fullPath = false)
    {
        if ($configName === null) {
            return false;
        }
        if ($fullPath) {
            $buildPath = $configName;
        } else {
            $buildPath = sprintf(plugin_dir_path(dirname(__FILE__)) . '/config/%s.json', $configName);
        }
        if (!file_exists($buildPath)) {
            return false;
        }
        return file_get_contents($buildPath);
    }

    public static function getConfig($configName = null, $array = false)
    {
        if ($configName === null) {
            wp_die("Config name not valid");
        }
        $config = self::getConfigContents($configName);
        if (!$config) {
            wp_die(sprintf("Get config failed for (%s).", $configName));
        }
        return json_decode($config, $array);
    }
    public static function startsWith( $haystack, $needle ) {
        $length = strlen( $needle );
        return substr( $haystack, 0, $length ) === $needle;
    }

    public static function endsWith( $haystack, $needle ) {
        $length = strlen( $needle );
        if( !$length ) {
            return true;
        }
        return substr( $haystack, -$length ) === $needle;
    }

    public static function snakeCaseCamelCase(string $input, ?string $separator = "_")
    {
        $splitFilename = explode($separator, $input);
        $makeCamelCase = array_map(function ($value) {
            return ucfirst($value);
        }, $splitFilename);

        return implode("", $makeCamelCase);
    }

	public static function validateCallback($callbackData)
	{
		$callbackResult = [
			'status' => 'error'
		];
		if (is_array($callbackData) && count($callbackData) === 2) {
			$callbackObject = $callbackData[0];
			$callbackMethod = $callbackData[1];

			$callbackResult['callback_method'] = $callbackMethod;
			$callbackResult['callback_object'] = $callbackObject;
			if (
				class_exists(get_class($callbackObject)) &&
				method_exists($callbackObject, $callbackMethod)
			) {
				$callbackResult['status'] = 'success';
				$callbackResult['callback_type'] = 'class_method';
			}
		} elseif ($callbackData instanceof \Closure) {
			$callbackResult['status'] = 'success';
			$callbackResult['callback_type'] = 'closure';
			$callbackResult['callback_method'] = $callbackData;
		} else {
			return false;
		}

		return $callbackResult;
	}

	public static function flattenArrayByKey(string $key, array $data) {
		return array_map(function ($item) use($key) {
			if (isset($item[$key])) {
				return $item[$key];
			}
			return $item;
		}, $data);
	}

	public static function callbackHandler($callbackData, ...$callbackMethodParams)
	{
		$validateCallback = self::validateCallback($callbackData);
		$callbackResult = false;
		if (!is_array($validateCallback)) {
			return false;
		}
		if (
			!isset($validateCallback['status']) ||
			$validateCallback['status'] === 'error' ||
			!isset($validateCallback['callback_type'])
		) {
			return false;
		}

		$callbackMethod = $validateCallback['callback_method'];
		switch ($validateCallback['callback_type']) {
			case 'closure':
				if ($callbackMethod instanceof \Closure) {
					$callbackResult = $callbackMethod(...$callbackMethodParams);
				}
				break;
			case 'class_method':
				$callbackObject = $validateCallback['callback_object'];
				if (
					class_exists(get_class($callbackObject)) &&
					method_exists($callbackObject, $callbackMethod)
				) {
					$callbackResult = $callbackObject->{$callbackMethod}(...$callbackMethodParams);
				}
				break;
			default:
				return $callbackMethod;
		}
		return $callbackResult;
	}

	public static function validateClasses($class, array $data) {
		foreach ($data as $item) {
			if ($item instanceof $class) {
				return true;
			}
		}
		return false;
	}

	public static function isArrayEmpty($data) {
		return (
			!isset($data) ||
			!is_array($data) ||
			!count($data)
		);
	}

	public static function appendArrayValues(array $sourceArray, array $destArray) {
		foreach ($destArray as $value) {
			$sourceArray[] = $value;
		}
		return $sourceArray;
	}

    public static function getRequestBooleanValue($value) {
        return isset($value) && ($value === '1' || $value === true || $value === 'true');
    }


    public static function loadPostType(string $postType) {
        $path = sprintf(
            '%s/post-types/%s/register-post-type.php',
            TRU_FETCHER_PLUGIN_ADMIN_RES_DIR,
            $postType
        );
        if (file_exists($path)) {
            require_once($path);
        }
    }

    public static function loadTaxonomy(string $taxonomy) {
        $path = sprintf(
            '%s/taxonomies/%s/register-taxonomy.php',
            TRU_FETCHER_PLUGIN_ADMIN_RES_DIR,
            $taxonomy
        );
        if (file_exists($path)) {
            require_once($path);
        }
    }
}
