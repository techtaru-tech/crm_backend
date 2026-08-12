<?php

declare(strict_types=1);

return [
    'url_empty'               => 'La URL está vacía.',
    'url_missing_scheme_host' => 'La URL debe incluir un esquema y un host.',
    'scheme_not_allowed'      => 'Solo se permiten los esquemas http y https.',
    'host_not_routable'       => 'El host :host no es un destino enrutable.',
    'host_unresolvable'       => 'No se pudo resolver el host :host.',
    'ipv6_not_allowed'        => 'Los destinos IPv6 no están permitidos actualmente.',
    'ip_unparseable'          => 'No se pudo analizar la IP :ip.',
    'ip_in_blocked_range'     => 'La IP :ip se resuelve a un rango privado/loopback/metadatos bloqueado.',
];
