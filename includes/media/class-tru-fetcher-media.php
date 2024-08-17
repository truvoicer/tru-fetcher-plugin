<?php
namespace TruFetcher\Includes\Media;

class Tru_Fetcher_Media {
    public const FILE_TYPES = [
        [
            'name' => 'image',
            'types' => [
                [
                    'name' => 'jpeg',
                    'mime_type' => 'image/jpeg',
                    'extension' => '.jpeg'
                ],
                [
                    'name' => 'jpg',
                    'mime_type' => 'image/jpg',
                    'extension' => '.jpg'
                ],
                [
                    'name' => 'gif',
                    'mime_type' => 'image/gif',
                    'extension' => '.gif'
                ],
                [
                    'name' => 'png',
                    'mime_type' => 'image/png',
                    'extension' => '.png'
                ],
                [
                    'name' => 'png',
                    'mime_type' => 'image/png',
                    'extension' => '.png'
                ],
                [
                    'name' => 'webp',
                    'mime_type' => 'image/webp',
                    'extension' => '.webp'
                ],
                [
                    'name' => 'svg',
                    'mime_type' => 'image/svg+xml',
                    'extension' => '.svg'
                ],
            ]
        ],
        [
            'name' => 'document',
            'types' => [
                [
                    'name' => 'csv',
                    'mime_type' => 'text/csv',
                    'extension' => '.csv'
                ],
                [
                    'name' => 'doc',
                    'mime_type' => 'application/msword',
                    'extension' => '.doc'
                ],
                [
                    'name' => 'docx',
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'extension' => '.docx'
                ],
                [
                    'name' => 'pdf',
                    'mime_type' => 'application/pdf',
                    'extension' => '.pdf'
                ],
                [
                    'name' => 'ppt',
                    'mime_type' => 'application/vnd.ms-powerpoint',
                    'extension' => '.ppt'
                ],
                [
                    'name' => 'pptx',
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'extension' => '.pptx'
                ],
                [
                    'name' => 'xls',
                    'mime_type' => 'application/vnd.ms-excel',
                    'extension' => '.xls'
                ],
                [
                    'name' => 'xlsx',
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'extension' => '.xlsx'
                ],
            ]
        ],
        [
            'name' => 'video',
            'types' => [

                [
                    'name' => 'avi',
                    'mime_type' => 'video/x-msvideo',
                    'extension' => '.avi'
                ],
                [
                    'name' => 'webm',
                    'mime_type' => 'video/webm',
                    'extension' => '.webm'
                ],
                [
                    'name' => 'wmv',
                    'mime_type' => 'video/x-ms-wmv',
                    'extension' => '.wmv'
                ],
                [
                    'name' => 'flv',
                    'mime_type' => 'video/x-flv',
                    'extension' => '.flv'
                ],
                [
                    'name' => '3gp',
                    'mime_type' => 'video/3gpp',
                    'extension' => '.3gp'
                ],
                [
                    'name' => '3g2',
                    'mime_type' => 'video/3gpp2',
                    'extension' => '.3g2'
                ],
                [
                    'name' => 'mkv',
                    'mime_type' => 'video/x-matroska',
                    'extension' => '.mkv'
                ],
                [
                    'name' => 'm4a',
                    'mime_type' => 'audio/mp4',
                    'extension' => '.m4a'
                ],
                [
                    'name' => 'm4v',
                    'mime_type' => 'video/x-m4v',
                    'extension' => '.m4v'
                ],
                [
                    'name' => 'ogg',
                    'mime_type' => 'audio/ogg',
                    'extension' => '.ogg'
                ],
                [
                    'name' => 'ogv',
                    'mime_type' => 'video/ogg',
                    'extension' => '.ogv'
                ],
                [
                    'name' => 'avi',
                    'mime_type' => 'video/x-msvideo',
                    'extension' => '.avi'
                ],
                [
                    'name' => 'webm',
                    'mime_type' => 'video/webm',
                    'extension' => '.webm'
                ],
                [
                    'name' => 'wmv',
                    'mime_type' => 'video/x-ms-wmv',
                    'extension' => '.wmv'
                ],
                [
                    'name' => 'flv',
                    'mime_type' => 'video/x-flv',
                    'extension' => '.flv'
                ],
                [
                    'name' => '3gp',
                    'mime_type' => 'video/3gpp',
                    'extension' => '.3gp'
                ]
            ]
        ],
        [
            'name' => 'audio',
            'types' => [

                [
                    'name' => 'mp3',
                    'mime_type' => 'audio/mpeg',
                    'extension' => '.mp3'
                ],
                [
                    'name' => 'mp4',
                    'mime_type' => 'video/mp4',
                    'extension' => '.mp4'
                ],
                [
                    'name' => 'mov',
                    'mime_type' => 'video/quicktime',
                    'extension' => '.mov'
                ],
                [
                    'name' => 'avi',
                    'mime_type' => 'video/x-msvideo',
                    'extension' => '.avi'
                ],
                [
                    'name' => 'wmv',
                    'mime_type' => 'video/x-ms-wmv',
                    'extension' => '.wmv'
                ],
                [
                    'name' => 'flv',
                    'mime_type' => 'video/x-flv',
                    'extension' => '.flv'
                ],
                [
                    'name' => 'ogg',
                    'mime_type' => 'audio/ogg',
                    'extension' => '.ogg'
                ],
                [
                    'name' => 'webm',
                    'mime_type' => 'video/webm',
                    'extension' => '.webm'
                ],
                [
                    'name' => '3gp',
                    'mime_type' => 'video/3gpp',
                    'extension' => '.3gp'
                ],
                [
                    'name' => '3g2',
                    'mime_type' => 'video/3gpp2',
                    'extension' => '.3g2'
                ],
                [
                    'name' => 'mkv',
                    'mime_type' => 'video/x-matroska',
                    'extension' => '.mkv'
                ],
                [
                    'name' => 'm4a',
                    'mime_type' => 'audio/mp4',
                    'extension' => '.m4a'
                ],
                [
                    'name' => 'm4v',
                    'mime_type' => 'video/x-m4v',
                    'extension' => '.m4v'
                ],
                [
                    'name' => 'ogg',
                    'mime_type' => 'audio/ogg',
                    'extension' => '.ogg'
                ],
                [
                    'name' => 'ogv',
                    'mime_type' => 'video/ogg',
                    'extension' => '.ogv'
                ],
            ]
        ],
    ];
    public static function getConfigByName(array $names, bool $parent) {
        $configs = [];
        foreach ($names as $name) {
            foreach (self::FILE_TYPES as $fileType) {
                if ($parent && $fileType['name'] === $name) {
                    $configs = [...$configs, ...$fileType['types']];
                    continue;
                }
                foreach ($fileType['types'] as $type) {
                    if ($type['name'] === $name) {
                        $configs[] = $type;
                    }
                }
            }
        }
        return $configs;
    }
}
