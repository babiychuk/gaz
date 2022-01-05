<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 12.06.2018
 * Time: 09:30
 */

namespace Drupal\gaz_migrate;

use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Password\PhpassHashedPassword;

class MODxPasswordService extends PhpassHashedPassword
implements
PasswordInterface
{
  public function check ($password, $hash) {
    if (substr($hash, 0, 6) == '$MODX$') {
      $stored_data = substr($hash, 6);
      $parts = preg_split('~/(?=[^/]*$)~', $stored_data);
      if (count($parts) == 2) {
        $stored_hash = $parts[0];
        $stored_salt = $parts[1];

        $current_hash = $this->modx_hash($password, $stored_salt);
        if ($current_hash == $stored_hash) {
          return TRUE;
        } else {
          return FALSE;
        }
      }
    }

    return parent::check($password, $hash);
  }

  protected function modx_hash($string, $salt) {
    if (is_string($salt) && strlen($salt) > 0) {
      $iterations = 1000;
      $derivedKeyLength = 32;
      $algorithm = 'sha256';
      $hashLength = strlen(hash($algorithm, null, true));
      $keyBlocks = ceil($derivedKeyLength / $hashLength);
      $derivedKey = '';
      for ($block = 1; $block <= $keyBlocks; $block++) {
        $hashBlock = $hb = hash_hmac($algorithm, $salt . pack('N', $block), $string, true);
        for ($blockIteration = 1; $blockIteration < $iterations; $blockIteration++) {
          $hashBlock ^= ($hb = hash_hmac($algorithm, $hb, $string, true));
        }
        $derivedKey .= $hashBlock;
      }
      $derivedKey = substr($derivedKey, 0, $derivedKeyLength);
      $derivedKey = base64_encode($derivedKey);
    } else {
      return '';
    }
    return $derivedKey;
  }
}
