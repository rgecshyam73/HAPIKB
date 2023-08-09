<?php 

namespace App\Helpers;

class IntlTimeZone {
/* Constants */
const int DISPLAY_SHORT = 1 ;
const int DISPLAY_LONG = 2 ;
/* Methods */
public static countEquivalentIDs ( string $zoneId ) : int
public static createDefault ( ) : IntlTimeZone
public static createEnumeration ([ mixed $countryOrRawOffset ] ) : IntlIterator
public static createTimeZone ( string $zoneId ) : IntlTimeZone
public static createTimeZoneIDEnumeration ( int $zoneType [, string $region [, int $rawOffset ]] ) : IntlIterator|false
public static fromDateTimeZone ( DateTimeZone $zoneId ) : IntlTimeZone
public static getCanonicalID ( string $zoneId [, bool &$isSystemID ] ) : string
public getDisplayName ([ bool $isDaylight [, int $style [, string $locale ]]] ) : string
public getDSTSavings ( ) : int
public static getEquivalentID ( string $zoneId , int $index ) : string
public getErrorCode ( ) : int
public getErrorMessage ( ) : string
public static getGMT ( ) : IntlTimeZone
public getID ( ) : string
public static getIDForWindowsID ( string $timezone [, string $region ] ) : string|false
public getOffset ( float $date , bool $local , int &$rawOffset , int &$dstOffset ) : bool
public getRawOffset ( ) : int
public static getRegion ( string $zoneId ) : string|false
public static getTZDataVersion ( ) : string
public static getUnknown ( ) : IntlTimeZone
public static getWindowsID ( string $timezone ) : string|false
public hasSameRules ( IntlTimeZone $otherTimeZone ) : bool
public toDateTimeZone ( ) : DateTimeZone
public useDaylightTime ( ) : bool
}

class IntlDateFormatter {
    public function __construct(
        ?string $locale,
        int $dateType = IntlDateFormatter::FULL,
        int $timeType = IntlDateFormatter::FULL,
        IntlTimeZone|DateTimeZone|string|null $timezone = null,
        IntlCalendar|int|null $calendar = null,
        ?string $pattern = null
    )
    public static function create(
        ?string $locale,
        int $dateType = IntlDateFormatter::FULL,
        int $timeType = IntlDateFormatter::FULL,
        IntlTimeZone|DateTimeZone|string|null $timezone = null,
        IntlCalendar|int|null $calendar = null,
        ?string $pattern = null
    ): ?IntlDateFormatter;
    public function format(IntlCalendar|DateTimeInterface|array|string|int|float $datetime): string|false;
    public function static formatObject(IntlCalendar|DateTimeInterface $datetime, array|int|string|null $format = null, ?string $locale = null): string|false;
    public function getCalendar(): int|false;
    public function getDateType(): int|false;
    public function getErrorCode(): int;
    public function getErrorMessage(): string;
    public function getLocale(int $type = ULOC_ACTUAL_LOCALE): string|false;
    public function getPattern(): string|false;
    public function getTimeType(): int|false;
    public function getTimeZoneId(): string|false;
    public function getCalendarObject(): IntlCalendar|false|null;
    public function getTimeZone(): IntlTimeZone|false;
    public function isLenient(): bool;
    public function localtime(string $string, int &$offset = null): array|false;
    public function parse(string $string, int &$offset = null): int|float|false;
    public function setCalendar(IntlCalendar|int|null $calendar): bool;
    public function setLenient(bool $lenient): void;
    public function setPattern(string $pattern): bool;
    public function setTimeZone(IntlTimeZone|DateTimeZone|string|null $timezone): ?bool;
}