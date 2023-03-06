<?php

namespace Serenity\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class DoNotDiscover implements DiscoveryAttribute
{
}
