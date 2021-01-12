<?php

use Cake\Routing\Router;
?>
<?php
//Carico le voci di menu extra portate da ogni plugin nel proprio element
$this->element('v-admin-extra-main-menu'); ?>
<div>
  <b-navbar toggleable="lg" type="dark" variant="info">
    <b-navbar-brand href="<?= Router::url(Router::url(['plugin' => false, 'prefix' => 'Admin', 'controller' => 'Pages', 'action' => 'display', 'admin'])) ?>">AngelCake CMS</b-navbar-brand>

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav>
        <b-nav-item href="<?= Router::url(['plugin' => false, 'prefix' => 'Admin', 'controller' => 'Articles', 'action' => 'index']) ?>">
          <b-icon-journal-text></b-icon-journal-text>
          Articoli
        </b-nav-item>
        <?= $this->fetch('extra-main-menu') ?>
        <b-nav-item href="/admin/static/index">
          <b-icon-markdown></b-icon-markdown>
          File Statici
        </b-nav-item>
        <b-nav-item href="/admin/blocks/index">
          <b-icon-bounding-box></b-icon-bounding-box>
          Blocchi
        </b-nav-item>
        <b-nav-item href="/users/index">
          <b-icon-people></b-icon-people>
          Utenti
        </b-nav-item>
      </b-navbar-nav>

      <!-- Right aligned nav items -->
      <b-navbar-nav class="ml-auto">
        <b-nav-item href="/" target="preview">

          Anteprima sito
          <b-icon-globe></b-icon-globe>
        </b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</div>