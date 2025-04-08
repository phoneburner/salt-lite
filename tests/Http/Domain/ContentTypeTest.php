<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Domain;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ContentTypeTest extends TestCase
{
    #[Test]
    public function constants(): void
    {
        self::assertSame('application/json', ContentType::JSON);
        self::assertSame('application/hal+json', ContentType::HAL_JSON);
        self::assertSame('application/health+json', ContentType::HEALTH_JSON);
        self::assertSame('image/png', ContentType::PNG);
        self::assertSame('text/html', ContentType::HTML);
        self::assertSame('application/problem+json', ContentType::PROBLEM_DETAILS_JSON);
        self::assertSame('text/plain', ContentType::TEXT);
        self::assertSame('text/csv', ContentType::CSV);
        self::assertSame('application/octet-stream', ContentType::OCTET_STREAM);
        self::assertSame('application/zip', ContentType::ZIP);
        self::assertSame('application/x-php', ContentType::PHP);
        self::assertSame('image/gif', ContentType::GIF);
        self::assertSame('text/css', ContentType::CSS);
        self::assertSame('text/javascript', ContentType::JS);
        self::assertSame('audio/x-aiff', ContentType::AIFF);
        self::assertSame('video/avi', ContentType::AVI);
        self::assertSame('image/bmp', ContentType::BMP);
        self::assertSame('application/x-bz2', ContentType::BZ2);
        self::assertSame('application/x-apple-diskimage', ContentType::DMG);
        self::assertSame('application/msword', ContentType::DOC);
        self::assertSame('application/vnd.openxmlformats-officedocument.wordprocessingml.document', ContentType::DOCX);
        self::assertSame('image/jpeg', ContentType::JPEG);
        self::assertSame('video/x-flv', ContentType::FLV);
        self::assertSame('application/gzip', ContentType::GZ);
        self::assertSame('message/rfc822', ContentType::EML);
        self::assertSame('application/postscript', ContentType::PS);
        self::assertSame('application/xml', ContentType::XML);
        self::assertSame('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', ContentType::XLSX);
        self::assertSame('audio/wav', ContentType::WAV);
        self::assertSame('application/excel', ContentType::XLS);
        self::assertSame('audio/x-ms-wmv', ContentType::WMV);
        self::assertSame('audio/x-ms-wma', ContentType::WMA);
        self::assertSame('text/x-vcard', ContentType::VCF);
        self::assertSame('application/x-font-truetype', ContentType::TTF);
        self::assertSame('image/tiff', ContentType::TIFF);
        self::assertSame('image/svg+xml', ContentType::SVG);
        self::assertSame('application/x-stuffit', ContentType::SIT);
        self::assertSame('application/x-tar', ContentType::TAR);
        self::assertSame('application/rtf', ContentType::RTF);
        self::assertSame('application/x-rar-compressed', ContentType::RAR);
        self::assertSame('application/vnd.openxmlformats-officedocument.presentationml.presentation', ContentType::PPTX);
        self::assertSame('application/vnd.ms-powerpoint', ContentType::PPT);
        self::assertSame('application/pdf', ContentType::PDF);
        self::assertSame('audio/ogg', ContentType::OGG);
        self::assertSame('application/vnd.oasis.opendocument.spreadsheet', ContentType::ODS);
        self::assertSame('application/vnd.oasis.opendocument.text', ContentType::ODT);
        self::assertSame('application/vnd.oasis.opendocument.presentation', ContentType::ODP);
        self::assertSame('application/vnd.oasis.opendocument.graphics', ContentType::ODG);
        self::assertSame('audio/mpeg', ContentType::MP3);
        self::assertSame('video/mp4', ContentType::MP4);
        self::assertSame('video/mpeg', ContentType::MPEG);
        self::assertSame('video/quicktime', ContentType::MOV);
        self::assertSame('audio/midi', ContentType::MIDI);
        self::assertSame('application/x-ms-dos-executable', ContentType::EXE);
        self::assertSame('application/stuffit', ContentType::HQX);
        self::assertSame('application/x-java-archive', ContentType::JAR);
        self::assertSame('audio/x-mpegurl', ContentType::M3U);
        self::assertSame('audio/mp4', ContentType::M4A);
        self::assertSame('application/x-msaccess', ContentType::MDB);
        self::assertSame('image/x-icon', ContentType::ICO);
        self::assertSame('application/yaml', ContentType::YAML);
        self::assertSame('text/calendar', ContentType::ICS);
    }

    #[Test]
    #[DataProvider('provideMimeTypeLookups')]
    public function lookupReturnsCorrectMimeType(string $extension, string $expectedMimeType): void
    {
        self::assertSame($expectedMimeType, ContentType::lookup($extension));
    }

    #[Test]
    #[DataProvider('provideUnknownExtensions')]
    public function lookupReturnsNullForUnknownExtensions(string $extension): void
    {
        self::assertNull(ContentType::lookup($extension));
    }

    #[Test]
    #[DataProvider('provideCaseSensitiveExtensions')]
    public function lookupIsCaseSensitive(string $extension): void
    {
        self::assertNull(ContentType::lookup($extension));
    }

    public static function provideMimeTypeLookups(): iterable
    {
        yield 'json' => ['json', ContentType::JSON];
        yield 'html' => ['html', ContentType::HTML];
        yield 'htm' => ['htm', ContentType::HTML];
        yield 'png' => ['png', ContentType::PNG];
        yield 'jpg' => ['jpg', ContentType::JPEG];
        yield 'jpeg' => ['jpeg', ContentType::JPEG];
        yield 'pdf' => ['pdf', ContentType::PDF];
        yield 'zip' => ['zip', ContentType::ZIP];
        yield 'php' => ['php', ContentType::PHP];
        yield 'php3' => ['php3', ContentType::PHP];
        yield 'php4' => ['php4', ContentType::PHP];
        yield 'php5' => ['php5', ContentType::PHP];
        yield 'bin' => ['bin', ContentType::OCTET_STREAM];
        yield 'so' => ['so', ContentType::OCTET_STREAM];
        yield 'o' => ['o', ContentType::OCTET_STREAM];
        yield 'a' => ['a', ContentType::OCTET_STREAM];
        yield 'doc' => ['doc', ContentType::DOC];
        yield 'docx' => ['docx', ContentType::DOCX];
        yield 'xls' => ['xls', ContentType::XLS];
        yield 'xlsx' => ['xlsx', ContentType::XLSX];
        yield 'ppt' => ['ppt', ContentType::PPT];
        yield 'pptx' => ['pptx', ContentType::PPTX];
        yield 'mp3' => ['mp3', ContentType::MP3];
        yield 'mp4' => ['mp4', ContentType::MP4];
        yield 'wav' => ['wav', ContentType::WAV];
        yield 'sln' => ['sln', ContentType::WAV];
        yield 'ogg' => ['ogg', ContentType::OGG];
        yield 'mov' => ['mov', ContentType::MOV];
        yield 'gif' => ['gif', ContentType::GIF];
        yield 'css' => ['css', ContentType::CSS];
        yield 'js' => ['js', ContentType::JS];
        yield 'aif' => ['aif', ContentType::AIFF];
        yield 'aiff' => ['aiff', ContentType::AIFF];
        yield 'avi' => ['avi', ContentType::AVI];
        yield 'bmp' => ['bmp', ContentType::BMP];
        yield 'bz2' => ['bz2', ContentType::BZ2];
        yield 'csv' => ['csv', ContentType::CSV];
        yield 'dmg' => ['dmg', ContentType::DMG];
        yield 'eml' => ['eml', ContentType::EML];
        yield 'eps' => ['eps', ContentType::PS];
        yield 'ps' => ['ps', ContentType::PS];
        yield 'exe' => ['exe', ContentType::EXE];
        yield 'flv' => ['flv', ContentType::FLV];
        yield 'gz' => ['gz', ContentType::GZ];
        yield 'hqx' => ['hqx', ContentType::HQX];
        yield 'ics' => ['ics', ContentType::ICS];
        yield 'jar' => ['jar', ContentType::JAR];
        yield 'm3u' => ['m3u', ContentType::M3U];
        yield 'm4a' => ['m4a', ContentType::M4A];
        yield 'mdb' => ['mdb', ContentType::MDB];
        yield 'mid' => ['mid', ContentType::MIDI];
        yield 'midi' => ['midi', ContentType::MIDI];
        yield 'mpg' => ['mpg', ContentType::MPEG];
        yield 'mpeg' => ['mpeg', ContentType::MPEG];
        yield 'odg' => ['odg', ContentType::ODG];
        yield 'odp' => ['odp', ContentType::ODP];
        yield 'odt' => ['odt', ContentType::ODT];
        yield 'ods' => ['ods', ContentType::ODS];
        yield 'rar' => ['rar', ContentType::RAR];
        yield 'rtf' => ['rtf', ContentType::RTF];
        yield 'tar' => ['tar', ContentType::TAR];
        yield 'sit' => ['sit', ContentType::SIT];
        yield 'svg' => ['svg', ContentType::SVG];
        yield 'tif' => ['tif', ContentType::TIFF];
        yield 'tiff' => ['tiff', ContentType::TIFF];
        yield 'ttf' => ['ttf', ContentType::TTF];
        yield 'vcf' => ['vcf', ContentType::VCF];
        yield 'wma' => ['wma', ContentType::WMA];
        yield 'wmv' => ['wmv', ContentType::WMV];
        yield 'xml' => ['xml', ContentType::XML];
        yield 'ico' => ['ico', ContentType::ICO];
        yield 'yaml' => ['yaml', ContentType::YAML];
        yield 'yml' => ['yml', ContentType::YAML];
        yield 'bpk' => ['bpk', ContentType::OCTET_STREAM];
        yield 'deploy' => ['deploy', ContentType::OCTET_STREAM];
        yield 'dist' => ['dist', ContentType::OCTET_STREAM];
        yield 'distz' => ['distz', ContentType::OCTET_STREAM];
        yield 'dms' => ['dms', ContentType::OCTET_STREAM];
        yield 'dump' => ['dump', ContentType::OCTET_STREAM];
        yield 'elc' => ['elc', ContentType::OCTET_STREAM];
        yield 'lha' => ['lha', ContentType::OCTET_STREAM];
        yield 'lrf' => ['lrf', ContentType::OCTET_STREAM];
        yield 'lzh' => ['lzh', ContentType::OCTET_STREAM];
        yield 'obj' => ['obj', ContentType::OCTET_STREAM];
        yield 'pkg' => ['pkg', ContentType::OCTET_STREAM];
    }

    public static function provideUnknownExtensions(): iterable
    {
        yield 'unknown' => ['unknown'];
        yield 'empty' => [''];
        yield 'xyz' => ['xyz'];
    }

    public static function provideCaseSensitiveExtensions(): iterable
    {
        yield 'JSON' => ['JSON'];
        yield 'HTML' => ['HTML'];
        yield 'PNG' => ['PNG'];
    }
}
