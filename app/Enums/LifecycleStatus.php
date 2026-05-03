<?php

namespace App\Enums;

enum LifecycleStatus: string
{
  case Planning = 'planning';
  case InProgress = 'in_progress';
  case Post = 'post';
  case Completed = 'completed';
}