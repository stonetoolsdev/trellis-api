<?php

namespace App\Enums;

enum SubmissionStatus: string
{
  case Draft = 'draft';
  case PendingReview = 'pending_review';
  case Approved = 'approved';
  case Rejected = 'rejected';
}