<?php
/**
 * This file is part of the SpamZap2 package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace App\Domain\Db\Schema;

use GreenFedora\Db\Schema\Table;

/**
 * Settings table.
 */
class SettingsTable extends Table
{
    /**
     * Initialisation.
     *
     * @return  void
     */
    protected function init()
    {
        $this->addColumnVarCharPrimary(32)
            ->addColumnText('value');
    }

    /**
     * Called after a successful create.
     *
     * @return  void
     */
    protected function postCreate()
    {
        $this->db->insert('settings', ['settings_id' => 'ignore-if-logged-in', 'value' => '1'])->execute();
        $this->db->insert('settings', ['settings_id' => 'debug-mode', 'value' => '0'])->execute();
        $this->db->insert('settings', ['settings_id' => 'dummy-mode', 'value' => '0'])->execute();
        $this->db->insert('settings', ['settings_id' => 'comment-chars', 'value' => '500'])->execute();
        $this->db->insert('settings', ['settings_id' => 'check-registration', 'value' => '1'])->execute();
        $this->db->insert('settings', ['settings_id' => 'check-comments', 'value' => '1'])->execute();
        $this->db->insert('settings', ['settings_id' => 'check-contacts', 'value' => '0'])->execute();
        $this->db->insert('settings', ['settings_id' => 'log-lines', 'value' => '250'])->execute();
        $this->db->insert('settings', ['settings_id' => 'log-count', 'value' => '0'])->execute();
    }
}
