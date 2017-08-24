<?php

/**
 * @file
 * Contains \Drupal\islandora_basic_collection\Form\IslandoraBasicCollectionAdmin.
 */

namespace Drupal\islandora_basic_collection\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class IslandoraBasicCollectionAdmin extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'islandora_basic_collection_admin';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('islandora_basic_collection.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['islandora_basic_collection.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $backend_options = \Drupal::moduleHandler()->invokeAll('islandora_basic_collection_query_backends');
    $map_to_title = function ($backend) {
      return $backend['title'];
    };

    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // // @FIXME
    // // Could not extract the default value because it is either indeterminate, or
    // // not scalar. You'll need to provide a default value in
    // // config/install/islandora_basic_collection.settings.yml and config/schema/islandora_basic_collection.schema.yml.
    // $form = array(
    //     // Display options.
    //     'display_generation_fieldset' => array(
    //       '#type' => 'fieldset',
    //       '#title' => t('Display Generation'),
    //       '#states' => array(
    //         'invisible' => array(
    //           ':input[name="islandora_basic_collection_disable_display_generation"]' => array('checked' => TRUE),
    //         ),
    //       ),
    //       'islandora_collection_display' => array(
    //         'islandora_basic_collection_page_size' => array(
    //           '#type' => 'textfield',
    //           '#title' => t('Default collection objects per page'),
    //           '#default_value' => \Drupal::config('islandora_basic_collection.settings')->get('islandora_basic_collection_page_size'),
    //           '#description' => t('The default number of objects to show in a collection view.'),
    //         ),
    //         'islandora_basic_collection_disable_count_object' => array(
    //           '#type' => 'checkbox',
    //           '#title' => t('Disable object count query in collection overview'),
    //           '#default_value' => \Drupal::config('islandora_basic_collection.settings')->get('islandora_basic_collection_disable_count_object'),
    //           '#description' => t("Disabling the object count query can improve performance when loading the overview for large collections."),
    //         ),
    //         'islandora_basic_collection_default_view' => array(
    //           '#type' => 'select',
    //           '#title' => t('Default collection view style.'),
    //           '#default_value' => \Drupal::config('islandora_basic_collection.settings')->get('islandora_basic_collection_default_view'),
    //           '#options' => array(
    //             'list' => t('List'),
    //             'grid' => t('Grid'),
    //           ),
    //         ),
    //         'islandora_basic_collection_display_backend' => array(
    //           '#type' => 'radios',
    //           '#title' => t('Display Generation'),
    //           '#options' => array_map($map_to_title, $backend_options),
    //           '#default_value' => \Drupal::config('islandora_basic_collection.settings')->get('islandora_basic_collection_display_backend'),
    //         ),
    //       ),
    //     ),
    //     'islandora_basic_collection_disable_display_generation' => array(
    //       '#type' => 'checkbox',
    //       '#title' => t('Completely disable default collection display generation.'),
    //       '#default_value' => \Drupal::config('islandora_basic_collection.settings')->get('islandora_basic_collection_disable_display_generation'),
    //       '#description' => t("Disabling display generation allows for alternate collection displays to be used."),
    //     ),
    //     'islandora_basic_collection_admin_page_size' => array(
    //       '#type' => 'textfield',
    //       '#title' => t('Objects per page during collection management'),
    //       '#default_value' => \Drupal::config('islandora_basic_collection.settings')->get('islandora_basic_collection_admin_page_size'),
    //       '#description' => t('The number of child objects to show per page in the migrate/share/delete interface.'),
    //       '#element_validate' => array('element_validate_integer_positive'),
    //       '#required' => TRUE,
    //     ),
    //     'islandora_basic_collection_disable_collection_policy_delete' => array(
    //       '#type' => 'checkbox',
    //       '#title' => t('Disable deleting the collection policy'),
    //       '#default_value' => \Drupal::config('islandora_basic_collection.settings')->get('islandora_basic_collection_disable_collection_policy_delete'),
    //       '#description' => t("Disables the 'delete' link for the COLLECTION_POLICY datastream."),
    //     ),
    //     // Metadata display.
    //     'metadata_display_fieldset' => array(
    //       '#type' => 'fieldset',
    //       '#title' => t('Metadata display'),
    //       'islandora_collection_metadata_display' => array(
    //         '#type' => 'checkbox',
    //         '#title' => t('Display object metadata'),
    //         '#description' => t('Display object metadata below the collection display.'),
    //         '#default_value' => variable_get('islandora_collection_metadata_display', FALSE),
    //       ),
    //     ),
    //   );


    // Define the elements that appear on a collection objects display page.
    // The key's match up with the form elements array keys.
    $page_content = [
      'description' => [
        'name' => t("Description"),
        'description' => t("An objects description field"),
      ],
      'collections' => [
        'name' => t("In Collection"),
        'description' => t("Indicates which collections this object belongs to"),
      ],
      'wrapper' => [
        'name' => t("Fieldset Metadata"),
        'description' => t("An objects metadata collection set"),
      ],
      'islandora_basic_collection_display' => [
        'name' => t("Object Content"),
        'description' => t("Main object page content, such as configured viewers"),
      ],
    ];

    $form['metadata_display_fieldset']['islandora_basic_collection_metadata_info_table_drag_attributes'] = [
      '#theme_wrappers' => [
        'fieldset'
        ],
      '#tree' => TRUE,
      '#title' => t("Page content placement"),
      '#theme' => 'islandora_basic_collection_metadata_table_drag_components',
      '#description' => t('Use the table below to determine the rendering order of page and metadata content.'),
      '#states' => [
        'visible' => [
          ':input[name="islandora_collection_metadata_display"]' => [
            'checked' => TRUE
            ],
        ]
        ],
    ];

    // @FIXME
    // Could not extract the default value because it is either indeterminate, or
    // not scalar. You'll need to provide a default value in
    // config/install/islandora_basic_collection.settings.yml and config/schema/islandora_basic_collection.schema.yml.
    $config = \Drupal::config('islandora_basic_collection.settings')->get('islandora_basic_collection_metadata_info_table_drag_attributes');

    foreach ($page_content as $key => $data) {
      $form['metadata_display_fieldset']['islandora_basic_collection_metadata_info_table_drag_attributes'][$key] = [];
      $element = & $form['metadata_display_fieldset']['islandora_basic_collection_metadata_info_table_drag_attributes'][$key];
      if (!isset($config[$key])) {
        $config[$key] = [];
      }
      $config[$key] += [
        'weight' => 0,
        'omit' => 0,
      ];

      $element['#weight'] = $config[$key]['weight'];
      $element['label'] = [
        '#type' => 'item',
        '#markup' => $data['name'],
      ];
      $element['textfield'] = [
        '#type' => 'item',
        '#markup' => $data['description'],
      ];
      $element['weight'] = [
        '#type' => 'textfield',
        '#default_value' => $element['#weight'],
        '#size' => 3,
        '#attributes' => [
          'class' => [
            'item-row-weight'
            ]
          ],
      ];
      $element['omit'] = [
        '#type' => 'checkbox',
        '#default_value' => $config[$key]['omit'],
        '#attributes' => [
          'class' => [
            'item-row-weight'
            ],
          'title' => t('Hide the selected element from display, marking it as an invisible element in the DOM.'),
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

}
?>
