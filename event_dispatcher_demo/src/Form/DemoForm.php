<?php

/**
 * @file
 * Contains Drupal\event_dispatcher_demo\Form\DemoForm.
 */

namespace Drupal\event_dispatcher_demo\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\event_dispatcher_demo\EventDispatcherDemoEvent;
use Drupal\event_dispatcher_demo\EventDispatcherDemoEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DemoForm extends ConfigFormBase {

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcher
   */
  protected $event_dispatcher;

  /**
   * @inheritDoc
   */
  public function __construct(EventDispatcher $eventDispatcher, ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
    $this->event_dispatcher = $eventDispatcher;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_dispatcher'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'demo_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('event_dispatcher_demo.demo_form_config');
    $form['my_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('My name'),
      '#default_value' => $config->get('my_name'),
    ];
    $form['my_website'] = [
      '#type' => 'textfield',
      '#title' => $this->t('My website'),
      '#default_value' => $config->get('my_website'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);

    // Get the config object.
    $config = $this->config('event_dispatcher_demo.demo_form_config');

    // Set the values the user submitted in the form.
    $config->set('my_name', $form_state->getValue('my_name'))
      ->set('my_website', $form_state->getValue('my_website'));

    // Dispatch the event and return it.
    $event = $this->event_dispatcher->dispatch(EventDispatcherDemoEvents::SAVE, new EventDispatcherDemoEvent($config));

    // Get all the data from the altered config object.
    $newData = $event->getConfig()->get();

    // Merge into the config the new data coming from the dispatcher (values can be overwritten).
    $config->merge($newData);

    // Save the config.
    $config->save();
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return [
      'event_dispatcher_demo.demo_form_config',
    ];
  }
}
