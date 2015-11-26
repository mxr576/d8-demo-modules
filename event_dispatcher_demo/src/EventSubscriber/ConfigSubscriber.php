<?php

/**
 * @file
 * Contains Drupal\event_dispatcher_demo\EventSubscriber\ConfigSubscriber.
 */

namespace Drupal\event_dispatcher_demo\EventSubscriber;

use Drupal\event_dispatcher_demo\DemoEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface {

  /**
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events['demo_form.save'][] = ['onConfigSave', 0];
    return $events;
  }

  /**
   * @param DemoEvent $event
   */
  public function onConfigSave($event) {
    // Get the config object from the event.
    $config = $event->getConfig();

    // Create a new value to be stored in the config and set it.
    $name_website = $config->get('my_name') . " / " . $config->get('my_website');
    $config->set('my_name_website', $name_website);

  }

}
