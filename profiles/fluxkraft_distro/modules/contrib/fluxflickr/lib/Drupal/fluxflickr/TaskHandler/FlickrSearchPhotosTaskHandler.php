<?php

/**
 * @file
 * Contains FlickrSearchPhotosTaskHandler.
 */

namespace Drupal\fluxflickr\TaskHandler;

/**
 * Event dispatcher for Flickr searches.
 */
class FlickrSearchPhotosTaskHandler extends FlickrTaskHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function runTask() {
    // Assemble the request arguments.
    $arguments = array(
      'text' => $this->task['data']['search'],
      'tags' => $this->task['data']['tags'],
      'tags_mode' => $this->task['data']['tags_mode'],
      'sort' => 'date-posted-asc',
      'privacy_filter' => 1,
      'format' => "json",
      'extras' => 'date_upload, original_format, owner_name, media',
    );
    // We store the upload date of the last Photo that was processed so
    // that we can benefit from the 'min_upload_date' query argument.
    $store = fluxservice_key_value('fluxflickr.search');
    if ($min_upload_date = $store->get($this->task['identifier'])) {
      $arguments['min_upload_date'] = $min_upload_date;
    }
    else {
      $date_array = $this->task['data']['min_upload_date'];
      $timestamp = strtotime($date_array['month'] . '/' . $date_array['day'] . '/' . $date_array['year']);
      $arguments['min_upload_date'] = $timestamp;
    }

    $account = $this->getAccount();

    if (($response = $account->client()->searchPhotos($arguments)) && $photos = $response['photos']['photo']) {

      $photos = fluxservice_entify_bycatch_multiple($photos, 'fluxflickr_photo', $account);
      foreach ($photos as $photo) {
        $event = $this->getEvent();
        rules_invoke_event($event, $account, $photo, $this->task['data']['search'], $this->task['data']['tags'], $this->task['data']['tags_mode'], $this->task['date']);
      }

      // Store the upload date of the last Photo that was processed.
      $last = end($photos);
      if (!empty($last)) {
        $store->set($this->task['identifier'], $last->dateupload);
      }
    }
  }
}
