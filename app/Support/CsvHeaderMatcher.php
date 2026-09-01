<?php

declare(strict_types=1);

namespace App\Support;

/**
 * One place that decides which lead field a CSV column heading means.
 *
 * Both import paths — the mapping wizard (CreateLeadImport) and the
 * vendor auto-detect importer (CrmCsvImporter) — used to keep their own
 * private alias tables.  They drifted: the wizard had no "full name"
 * concept at all, so a file whose only name column was `Name` imported
 * every lead nameless, while the auto-detect path handled it fine.  A
 * single table means a heading learned once is understood everywhere.
 *
 * Keys returned here are CANONICAL.  Callers translate them to whatever
 * they call the field internally (the wizard says `assigned_user`, the
 * importer says `assigned`).
 */
class CsvHeaderMatcher
{
    /** @var array<string, array<int, string>> canonical field => normalized heading spellings */
    public const ALIASES = [
        'source_id'   => ['leadid', 'recordid', 'externalid', 'crmid', 'id', 'reference', 'refnumber', 'srnumber'],
        'first_name'  => ['firstname', 'givenname', 'fname'],
        'last_name'   => ['lastname', 'surname', 'familyname', 'lname'],
        'full_name'   => ['name', 'fullname', 'contactname', 'leadname', 'customername', 'clientname', 'personname'],
        'email'       => ['email', 'emailaddress', 'primaryemail', 'workemail', 'emailid', 'mailid'],
        // "Contact no", "Mobile No.", "Phone Number" and "Contact Number" are
        // the same column with four spellings — the trailing-word fallback in
        // match() folds the first two onto the last two.
        'phone'       => ['phone', 'phonenumber', 'mobile', 'mobilephone', 'mobilenumber', 'contactnumber',
                          'primaryphone', 'whatsapp', 'whatsappnumber', 'cell', 'cellphone', 'telephone', 'tel', 'contact'],
        'company'     => ['company', 'companyname', 'organization', 'organisation', 'accountname', 'firm', 'business'],
        'city'        => ['city', 'town', 'location'],
        'source'      => ['leadsource', 'source', 'originalsource', 'channel', 'campaign'],
        'status'      => ['leadstatus', 'status', 'stage', 'leadstage'],
        'priority'    => ['leadtype', 'priority', 'temperature', 'leadtemperature', 'rating', 'leadquality'],
        'deal_value'  => ['budget', 'dealvalue', 'amount', 'value', 'revenue', 'expectedrevenue', 'dealsize', 'price'],
        'assigned'    => ['assignedto', 'owner', 'leadowner', 'salesrep', 'assigneduser', 'accountowner',
                          'hubspotowner', 'assignee', 'agent', 'handledby'],
        'follow_up'   => ['nextfollowup', 'followupdate', 'nextfollowupdate', 'nextactiondate', 'followup', 'nextcall'],
        'contacted'   => ['lastcontacted', 'lastcontact', 'lastactivitydate', 'lastactivity', 'lastcalled'],
        'notes'       => ['notes', 'note', 'description', 'comments', 'remarks', 'message', 'requirements'],
    ];

    /** Lowercase, letters+digits only — so heading spelling stops mattering. */
    public static function normalize(string $header): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($header)) ?? '';
    }

    /**
     * Canonical field for a CSV heading, or null when nothing matches
     * (caller decides whether that becomes "Skip" or a custom field).
     */
    public static function match(string $header): ?string
    {
        $norm = self::normalize($header);

        if ($norm === '') {
            return null;
        }

        if ($field = self::lookup($norm)) {
            return $field;
        }

        // Second pass: Indian and SMB exports abbreviate the trailing word
        // ("Contact no", "Mobile No.", "Ph num").  Expanding it only AFTER
        // an exact miss keeps innocent headings safe — "Info" would expand
        // to "inumber", which matches nothing and so still falls through.
        $expanded = preg_replace('/(nos?|num|nmbr)$/', 'number', $norm);

        if (is_string($expanded) && $expanded !== $norm) {
            if ($field = self::lookup($expanded)) {
                return $field;
            }
        }

        // "Ph"/"Ph." is common enough in hand-kept sheets to be worth a case.
        if ($norm === 'ph' || $norm === 'phno' || $norm === 'phnumber') {
            return 'phone';
        }

        return null;
    }

    private static function lookup(string $norm): ?string
    {
        foreach (self::ALIASES as $field => $aliases) {
            if (in_array($norm, $aliases, true)) {
                return $field;
            }
        }

        return null;
    }
}
