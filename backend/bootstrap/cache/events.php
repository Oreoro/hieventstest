<?php return array (
  'HiEvents\\Providers\\EventServiceProvider' => 
  array (
    'HiEvents\\Events\\OrderStatusChangedEvent' => 
    array (
      0 => 'HiEvents\\Listeners\\Order\\SendOrderDetailsEmailListener@handle',
      1 => 'HiEvents\\Listeners\\Event\\UpdateEventStatsListener@handle',
    ),
    'Illuminate\\Auth\\Events\\Registered' => 
    array (
      0 => 'Illuminate\\Auth\\Listeners\\SendEmailVerificationNotification',
    ),
  ),
);