<?php

namespace BoundedContext\Contracts\Workflow;

use BoundedContext\Contracts\Core\Playable;
use BoundedContext\Contracts\Core\ResetableByGenerator;
use BoundedContext\Contracts\Core\Snapshotable;

interface Workflow extends ResetableByGenerator, Playable, Snapshotable
{

}