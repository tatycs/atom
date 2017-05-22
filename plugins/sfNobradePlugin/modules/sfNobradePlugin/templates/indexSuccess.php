<?php decorate_with('layout_3col') ?>

<?php slot('sidebar') ?>
  <?php include_component('informationobject', 'contextMenu') ?>
<?php end_slot() ?>

<?php slot('title') ?>

  <h1><?php echo render_title($nobrade) ?></h1>

  <?php if (isset($errorSchema)): ?>
    <div class="messages error">
      <ul>
        <?php foreach ($errorSchema as $error): ?>
          <li><?php echo $error->getMessage(ESC_RAW) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (QubitInformationObject::ROOT_ID != $resource->parentId): ?>
    <?php echo include_partial('default/breadcrumb', array('resource' => $resource, 'objects' => $resource->getAncestors()->andSelf()->orderBy('lft'))) ?>
  <?php endif; ?>

  <?php echo get_component('default', 'translationLinks', array('resource' => $resource)) ?>

<?php end_slot() ?>

<?php slot('context-menu') ?>

  <?php echo get_partial('informationobject/actionIcons', array('resource' => $resource)) ?>

  <?php echo get_partial('informationobject/subjectAccessPoints', array('resource' => $resource, 'sidebar' => true)) ?>

  <?php echo get_partial('informationobject/nameAccessPoints', array('resource' => $resource, 'sidebar' => true)) ?>

  <?php echo get_partial('informationobject/genreAccessPoints', array('resource' => $resource, 'sidebar' => true)) ?>

  <?php echo get_partial('informationobject/placeAccessPoints', array('resource' => $resource, 'sidebar' => true)) ?>

  <?php if (check_field_visibility('app_element_visibility_physical_storage')): ?>
    <?php echo get_component('physicalobject', 'contextMenu', array('resource' => $resource)) ?>
  <?php endif; ?>

<?php end_slot() ?>

<?php slot('before-content') ?>

  <?php echo get_component('digitalobject', 'imageflow', array('resource' => $resource)) ?>

<?php end_slot() ?>

<?php if (0 < count($resource->digitalObjects)): ?>
  <?php echo get_component('digitalobject', 'show', array('link' => $digitalObjectLink, 'resource' => $resource->digitalObjects[0], 'usageType' => QubitTerm::REFERENCE_ID)) ?>
<?php endif; ?>

<section id="identityArea">

  <?php if (check_field_visibility('app_element_visibility_nobrade_identity_area')): ?>
    <?php echo link_to_if(SecurityPrivileges::editCredentials($sf_user, 'informationObject'), '<h2>'.__('Identity Area').'</h2>', array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('anchor' => 'identityArea', 'title' => __('Edit identity area'))) ?>
  <?php endif; ?>

  <?php echo render_show(__('Reference Code'), render_value($nobrade->referenceCode), array('fieldLabel' => 'referenceCode')) ?>

  <?php echo render_show(__('Title'), render_value($resource->getTitle(array('cultureFallback' => true))), array('fieldLabel' => 'title')) ?>

  <div class="field">
    <h3><?php echo __('Date(s)') ?></h3>
    <div class="creationDates">
      <ul>
        <?php foreach ($resource->getDates() as $item): ?>
          <li>
            <?php echo Qubit::renderDateStartEnd($item->getDate(array('cultureFallback' => true)), $item->startDate, $item->endDate) ?> (<?php echo $item->getType(array('cultureFallback' => true)) ?>)
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <?php echo render_show(__('Level of Description'), render_value($resource->levelOfDescription), array('fieldLabel' => 'levelOfDescription')) ?>

  <?php echo render_show(__('Extent and Medium'), render_value($resource->getCleanExtentAndMedium(array('cultureFallback' => true))), array('fieldLabel' => 'extentAndMedium')) ?>
</section> <!-- /section#identityArea -->

<section id="contextArea">

  <?php if (check_field_visibility('app_element_visibility_nobrade_context_area')): ?>
    <?php echo link_to_if(SecurityPrivileges::editCredentials($sf_user, 'informationObject'), '<h2>'.__('Context Area').'</h2>', array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('anchor' => 'contextArea', 'title' => __('Edit Context Area'))) ?>
  <?php endif; ?>

  <div class="creatorHistories">
    <?php echo get_component('informationobject', 'creatorDetail', array(
      'resource' => $resource,
      'creatorHistoryLabels' => $creatorHistoryLabels)) ?>
  </div>

  <div class="relatedFunctions">
    <?php foreach ($functionRelations as $item): ?>
      <div class="field">
        <h3><?php echo __('Related Function')?></h3>
        <div>
          <?php echo link_to(render_title($item->subject->getLabel()), array($item->subject, 'module' => 'function')) ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="repository">
    <?php echo render_show_repository(__('Repository'), $resource) ?>
  </div>

  <?php if (check_field_visibility('app_element_visibility_nobrade_archival_history')): ?>
    <?php echo render_show(__('Archival History'), render_value($resource->getArchivalHistory(array('cultureFallback' => true))), array('fieldLabel' => 'archivalHistory')) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_immediate_source')): ?>
    <?php echo render_show(__('Immediate Source of Acquisition or Transfer'), render_value($resource->getAcquisition(array('cultureFallback' => true))), array('fieldLabel' => 'immediateSourceOfAcquisitionOrTransfer')) ?>
  <?php endif; ?>

</section> <!-- /section#contextArea -->

<section id="contentAndStructureArea">

  <?php if (check_field_visibility('app_element_visibility_nobrade_content_and_structure_area')): ?>
    <?php echo link_to_if(SecurityPrivileges::editCredentials($sf_user, 'informationObject'), '<h2>'.__('Content and Structure Area').'</h2>', array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('anchor' => 'contentAndStructureArea', 'title' => __('Edit Content and Structure Area'))) ?>
  <?php endif; ?>

  <?php echo render_show(__('Scope and Content'), render_value($resource->getScopeAndContent(array('cultureFallback' => true))), array('fieldLabel' => 'scopeAndContent')) ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_appraisal_destruction')): ?>
    <?php echo render_show(__('Appraisal, Destruction and Scheduling'), render_value($resource->getAppraisal(array('cultureFallback' => true))), array('fieldLabel' => 'appraisalDestructionAndScheduling')) ?>
  <?php endif; ?>

  <?php echo render_show(__('Accruals'), render_value($resource->getAccruals(array('cultureFallback' => true))), array('fieldLabel' => 'accruals')) ?>

  <?php echo render_show(__('System of Arrangement'), render_value($resource->getArrangement(array('cultureFallback' => true))), array('fieldLabel' => 'systemOfArrangement')) ?>
</section> <!-- /section#contentAndStructureArea -->

<section id="conditionsOfAccessAndUseArea">

  <?php if (check_field_visibility('app_element_visibility_nobrade_conditions_of_access_use_area')): ?>
    <?php echo link_to_if(SecurityPrivileges::editCredentials($sf_user, 'informationObject'), '<h2>'.__('Conditions of Access and Use Area').'</h2>', array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('anchor' => 'conditionsOfAccessAndUseArea', 'title' => __('Edit conditions of access and use area'))) ?>
  <?php endif; ?>

  <?php echo render_show(__('Conditions Governing Access'), render_value($resource->getAccessConditions(array('cultureFallback' => true))), array('fieldLabel' => 'conditionsGoverningAccess')) ?>

  <?php echo render_show(__('Conditions governing reproduction'), render_value($resource->getReproductionConditions(array('cultureFallback' => true))), array('fieldLabel' => 'conditionsGoverningReproduction')) ?>

  <div class="field">
    <h3><?php echo __('Language of Material') ?></h3>
    <div class="languageOfMaterial">
      <ul>
        <?php foreach ($resource->language as $code): ?>
          <li><?php echo format_language($code) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <div class="field">
    <h3><?php echo __('Script of Material') ?></h3>
    <div class="scriptOfMaterial">
      <ul>
        <?php foreach ($resource->script as $code): ?>
          <li><?php echo format_script($code) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <?php echo render_show(__('Language and Script Notes'), render_value($nobrade->languageNotes), array('fieldLabel' => 'languageAndScriptNotes')) ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_physical_condition')): ?>
    <?php echo render_show(__('Physical Characteristics and Technical Requirements'), render_value($resource->getPhysicalCharacteristics(array('cultureFallback' => true))), array('fieldLabel' => 'physicalCharacteristics')) ?>
  <?php endif; ?>

  <?php echo render_show(__('Finding Aids'), render_value($resource->getFindingAids(array('cultureFallback' => true))), array('fieldLabel' => 'findingAids')) ?>
</section> <!-- /section#conditionsOfAccessAndUseArea -->

<section id="alliedMaterialsArea">

  <?php if (check_field_visibility('app_element_visibility_nobrade_allied_materials_area')): ?>
    <?php echo link_to_if(SecurityPrivileges::editCredentials($sf_user, 'informationObject'), '<h2>'.__('Allied Materials Area').'</h2>', array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('anchor' => 'alliedMaterialsArea', 'title' => __('Edit alied materials area'))) ?>
  <?php endif; ?>

  <?php echo render_show(__('Existence and Location of Originals'), render_value($resource->getLocationOfOriginals(array('cultureFallback' => true))), array('fieldLabel' => 'existenceAndLocationOfOriginals')) ?>

  <?php echo render_show(__('Existence and Location of Copies'), render_value($resource->getLocationOfCopies(array('cultureFallback' => true))), array('fieldLabel' => 'existenceAndLocationOfCopies')) ?>

  <?php echo render_show(__('Related Units of Description'), render_value($resource->getRelatedUnitsOfDescription(array('cultureFallback' => true))), array('fieldLabel' => 'relatedUnitsOfDescription')) ?>

  <div class="relatedMaterialDescriptions">
    <?php echo get_partial('informationobject/relatedMaterialDescriptions', array('resource' => $resource, 'template' => 'nobrade')) ?>
  </div>

  <?php foreach ($resource->getNotesByType(array('noteTypeId' => QubitTerm::PUBLICATION_NOTE_ID)) as $item): ?>
    <?php echo render_show(__('Publication Note'), render_value($item->getContent(array('cultureFallback' => true))), array('fieldLabel' => 'publicationNote')) ?>
  <?php endforeach; ?>
</section> <!-- /section#alliedMaterialsArea -->

<section id="notesArea">

  <?php if (check_field_visibility('app_element_visibility_nobrade_notes_area')): ?>
    <?php echo link_to_if(SecurityPrivileges::editCredentials($sf_user, 'informationObject'), '<h2>'.__('Notes Area').'</h2>', array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('anchor' => 'notesArea', 'title' => __('Edit Notes Area'))) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_notes')): ?>
    <?php foreach ($resource->getNotesByType(array('noteTypeId' => QubitTerm::GENERAL_NOTE_ID)) as $item): ?>
      <?php echo render_show(__('Note'), render_value($item->getContent(array('cultureFallback' => true))), array('fieldLabel' => 'generalNote')) ?>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="alternativeIdentifiers">
    <?php echo get_partial('informationobject/alternativeIdentifiersIndex', array('resource' => $resource)) ?>
  </div>
</section> <!-- /section#notesArea -->

<section id="accessPointsArea">

  <?php if (check_field_visibility('app_element_visibility_nobrade_access_points_area')): ?>
    <?php echo link_to_if(SecurityPrivileges::editCredentials($sf_user, 'informationObject'), '<h2>'.__('Access Points Area').'</h2>', array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('anchor' => 'accessPointsArea', 'title' => __('Edit Access Points Area'))) ?>
  <?php endif; ?>

  <div class="subjectAccessPoints">
    <?php echo get_partial('informationobject/subjectAccessPoints', array('resource' => $resource)) ?>
  </div>

  <div class="placeAccessPoints">
    <?php echo get_partial('informationobject/placeAccessPoints', array('resource' => $resource)) ?>
  </div>

  <div class="nameAccessPoints">
    <?php echo get_partial('informationobject/nameAccessPoints', array('resource' => $resource)) ?>
  </div>

  <div class="genreAccessPoints">
    <?php echo get_partial('informationobject/genreAccessPoints', array('resource' => $resource)) ?>
  </div>
</section> <!-- /section#accessPointsArea -->

<section id="descriptionControlArea">

  <?php if (check_field_visibility('app_element_visibility_nobrade_description_control_area')): ?>
    <?php echo link_to_if(SecurityPrivileges::editCredentials($sf_user, 'informationObject'), '<h2>'.__('Description Control Area').'</h2>', array($resource, 'module' => 'informationobject', 'action' => 'edit'), array('anchor' => 'descriptionControlArea', 'title' => __('Edit description control area'))) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_description_identifier')): ?>
    <?php echo render_show(__('Description Identifier'), render_value($resource->getDescriptionIdentifier(array('cultureFallback' => true))), array('fieldLabel' => 'descriptionIdentifier')) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_institution_identifier')): ?>
    <?php echo render_show(__('Institution Identifier'), render_value($resource->getInstitutionResponsibleIdentifier(array('cultureFallback' => true))), array('fieldLabel' => 'institutionIdentifier')) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_rules_conventions')): ?>
    <?php echo render_show(__('Rules and/or Conventions Used'), render_value($resource->getRules(array('cultureFallback' => true))), array('fieldLabel' => 'rulesAndOrConventionsUsed')) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_status')): ?>
    <?php echo render_show(__('Status'), render_value($resource->descriptionStatus), array('fieldLabel' => 'descriptionStatus')) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_level_of_detail')): ?>
    <?php echo render_show(__('Level of Detail'), render_value($resource->descriptionDetail), array('fieldLabel' => 'levelOfDetail')) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_dates')): ?>
    <?php echo render_show(__('Dates of Creation Revision Deletion'), render_value($resource->getRevisionHistory(array('cultureFallback' => true))), array('fieldLabel' => 'datesOfCreationRevisionDeletion')) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_languages')): ?>
    <div class="field">
      <h3><?php echo __('Language(s)') ?></h3>
      <div class="languages">
        <ul>
          <?php foreach ($resource->languageOfDescription as $code): ?>
            <li><?php echo format_language($code) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_scripts')): ?>
    <div class="field">
      <h3><?php echo __('Script(s)') ?></h3>
      <div class="scripts">
        <ul>
          <?php foreach ($resource->scriptOfDescription as $code): ?>
            <li><?php echo format_script($code) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_sources')): ?>
    <?php echo render_show(__('Sources'), render_value($resource->getSources(array('cultureFallback' => true))), array('fieldLabel' => 'sources')) ?>
  <?php endif; ?>

  <?php if (check_field_visibility('app_element_visibility_nobrade_control_archivists_notes')): ?>
    <?php foreach ($resource->getNotesByType(array('noteTypeId' => QubitTerm::ARCHIVIST_NOTE_ID)) as $item): ?>
      <?php echo render_show(__('Archivist\'s Note'), render_value($item->getContent(array('cultureFallback' => true))), array('fieldLabel' => 'archivistNote')) ?>
    <?php endforeach; ?>
  <?php endif; ?>

</section> <!-- /section#descriptionControlArea -->

<?php if ($sf_user->isAuthenticated()): ?>

  <div class="section" id="rightsArea">

    <h2><?php echo __('Rights Area') ?> </h2>

    <div class="relatedRights">
      <?php echo get_component('right', 'relatedRights', array('resource' => $resource)) ?>
    </div>

  </div> <!-- /section#rightsArea -->

<?php endif; ?>

<?php if (0 < count($resource->digitalObjects)): ?>

  <div class="digitalObjectMetadata">
    <?php echo get_partial('digitalobject/metadata', array('resource' => $resource->digitalObjects[0])) ?>
  </div>

  <div class="digitalObjectRights">
    <?php echo get_partial('digitalobject/rights', array('resource' => $resource->digitalObjects[0])) ?>
  </div>

<?php endif; ?>

<section id="accessionArea">

  <h2><?php echo __('Accession Area') ?></h2>

  <div class="accessions">
    <?php echo get_component('informationobject', 'accessions', array('resource' => $resource)) ?>
  </div>

</section> <!-- /section#accessionArea -->

<?php slot('after-content') ?>
  <?php echo get_partial('informationobject/actions', array('resource' => $resource, 'renameForm' => $renameForm)) ?>
<?php end_slot() ?>
