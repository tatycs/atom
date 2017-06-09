<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Information Object - editNobrade
 *
 * @package    AccesstoMemory
 * @subpackage informationObject - initialize an editIsad template for updating an information object
 * @author     Peter Van Garderen <peter@artefactual.com>
 * @author     Jesús García Crespo <correo@sevein.com>
 * @author     Tatiana Canelhas <tatycs@gmail.com>
 */
class sfNobradePluginEditAction extends InformationObjectEditAction
{
  // Arrays not allowed in class constants
  public static
    $NAMES = array(
      'accessConditions',
      'accruals',
      'acquisition',
      'appraisal',
      'archivalHistory',
      'arrangement',
      'creators',
      'descriptionDetail',
      'descriptionIdentifier',
      'extentAndMedium',
      'findingAids',
      'identifier',
      'institutionResponsibleIdentifier',
      'language',
      'languageNotes',
      'languageOfDescription',
      'levelOfDescription',
      'locationOfCopies',
      'locationOfOriginals',
      'nameAccessPoints',
      'genreAccessPoints',
      'physicalCharacteristics',
      'placeAccessPoints',
      'relatedUnitsOfDescription',
      'relatedMaterialDescriptions',
      'repository',
      'reproductionConditions',
      'revisionHistory',
      'rules',
      'scopeAndContent',
      'scriptOfDescription',
      'script',
      'sources',
      'subjectAccessPoints',
      'descriptionStatus',
      'displayStandard',
      'displayStandardUpdateDescendants',
      'title');

  protected function earlyExecute()
  {
    parent::earlyExecute();

    $this->nobrade = new sfNobradePlugin($this->resource);

    $title = $this->context->i18n->__('Add New Archival Description');
    if (isset($this->getRoute()->resource))
    {
      if (1 > strlen($title = $this->resource->__toString())) //STRLEN'S TITLE LESS THEN 1 = UNTITLE
      {
        $title = $this->context->i18n->__('Untitled');
      }

      $title = $this->context->i18n->__('Edit %1%', array('%1%' => $title));
    }

    $this->response->setTitle("$title - {$this->response->getTitle()}");

    $this->alternativeIdentifiersComponent = new InformationObjectAlternativeIdentifiersComponent($this->context, 'informationobject', 'alternativeIdentifiers');
    $this->alternativeIdentifiersComponent->resource = $this->resource;
    $this->alternativeIdentifiersComponent->execute($this->request);

    $this->eventComponent = new sfNobradePluginEventComponent($this->context, 'sfNobradePlugin', 'event');
    $this->eventComponent->resource = $this->resource;
    $this->eventComponent->execute($this->request);

    $this->publicationNotesComponent = new InformationObjectNotesComponent($this->context, 'informationobject', 'notes');
    $this->publicationNotesComponent->resource = $this->resource;
    $this->publicationNotesComponent->execute($this->request, $options = array('type' => 'nobradePublicationNotes'));

    $this->notesComponent = new InformationObjectNotesComponent($this->context, 'informationobject', 'notes');
    $this->notesComponent->resource = $this->resource;
    $this->notesComponent->execute($this->request, $options = array('type' => 'nobradeNotes'));

    $this->archivistsNotesComponent = new InformationObjectNotesComponent($this->context, 'informationobject', 'notes');
    $this->archivistsNotesComponent->resource = $this->resource;
    $this->archivistsNotesComponent->execute($this->request, $options = array('type' => 'nobradeArchivistsNotes'));

    //canelhas - NOBRADE 6.2
    $this->preservationNotesComponent = new InformationObjectNotesComponent($this->context, 'informationobject', 'notes');
    $this->preservationNotesComponent->resource = $this->resource;
    $this->preservationNotesComponent->execute($this->request, $options = array('type' => 'nobradePreservationNotes'));
  }

  protected function addField($name)
  {
    switch ($name)
    {
      case 'creators':
        $criteria = new Criteria;
        $criteria->add(QubitEvent::OBJECT_ID, $this->resource->id);
        $criteria->add(QubitEvent::ACTOR_ID, null, Criteria::ISNOTNULL);
        $criteria->add(QubitEvent::TYPE_ID, QubitTerm::CREATION_ID);

        $value = $choices = array();
        foreach ($this->events = QubitEvent::get($criteria) as $item)
        {
          $choices[$value[] = $this->context->routing->generate(null, array($item->actor, 'module' => 'actor'))] = $item->actor;
        }

        $this->form->setDefault('creators', $value);
        $this->form->setValidator('creators', new sfValidatorPass);
        $this->form->setWidget('creators', new sfWidgetFormSelect(array('choices' => $choices, 'multiple' => true)));

        break;

      case 'appraisal':
        $this->form->setDefault('appraisal', $this->resource['appraisal']);
        $this->form->setValidator('appraisal', new sfValidatorString);
        $this->form->setWidget('appraisal', new sfWidgetFormTextarea);

        break;

      case 'languageNotes':

        $this->form->setDefault('languageNotes', $this->nobrade['languageNotes']);
        $this->form->setValidator('languageNotes', new sfValidatorString);
        $this->form->setWidget('languageNotes', new sfWidgetFormTextarea);

        break;

      default:

        return parent::addField($name);
    }
  }

  protected function processField($field)
  {
    switch ($field->getName())
    {
      case 'creators':
        $value = $filtered = array();
        foreach ($this->form->getValue('creators') as $item)
        {
          $params = $this->context->routing->parse(Qubit::pathInfo($item));
          $resource = $params['_sf_route']->resource;
          $value[$resource->id] = $filtered[$resource->id] = $resource;
        }

        foreach ($this->events as $item)
        {
          if (isset($value[$item->actor->id]))
          {
            unset($filtered[$item->actor->id]);
          }
          else if (!isset($this->request->sourceId))
          {
            $item->delete();
          }
        }

        foreach ($filtered as $item)
        {
          $event = new QubitEvent;
          $event->actor = $item;
          $event->typeId = QubitTerm::CREATION_ID;

          $this->resource->eventsRelatedByobjectId[] = $event;
        }

        break;

      case 'languageNotes':

        $this->nobrade['languageNotes'] = $this->form->getValue('languageNotes');

        break;

      default:

        return parent::processField($field);
    }
  }

  protected function processForm()
  {
    $this->resource->sourceStandard = 'NOBRADE, Arquivo Nacional, 2006';

    $this->alternativeIdentifiersComponent->processForm();

    $this->eventComponent->processForm();

    $this->publicationNotesComponent->processForm();

    $this->notesComponent->processForm();

    $this->archivistsNotesComponent->processForm();

    $this->preservationNotesComponent->processForm();

    return parent::processForm();
  }

  /**
   * Update Nobrade's notes
   *
   * @param QubitInformationObject $informationObject
   */
  protected function updateNotes()
  {
    // Update archivist's notes (multiple) - NOBRADE 7.1
    foreach ((array) $this->request->new_archivist_note as $content)
    {
      if (0 < strlen($content))
      {
        $note = new QubitNote;
        $note->content = $content;
        $note->typeId = QubitTerm::ARCHIVIST_NOTE_ID;
        $note->userId = $this->context->user->getAttribute('user_id');

        $this->resource->notes[] = $note;
      }
    }

    // Update publication notes (multiple) - NOBRADE 5.4
    foreach ((array) $this->request->new_publication_note as $content)
    {
      if (0 < strlen($content))
      {
        $note = new QubitNote;
        $note->content = $content;
        $note->typeId = QubitTerm::PUBLICATION_NOTE_ID;
        $note->userId = $this->context->user->getAttribute('user_id');

        $this->resource->notes[] = $note;
      }
    }

    // Update general notes (multiple) - NOBRADE 6.2
    foreach ((array) $this->request->new_note as $content)
    {
      if (0 < strlen($content))
      {
        $note = new QubitNote;
        $note->content = $content;
        $note->typeId = QubitTerm::GENERAL_NOTE_ID;
        $note->userId = $this->context->user->getAttribute('user_id');

        $this->resource->notes[] = $note;
      }
    }
    // Update preservation notes (multiple) - NOBRADE 6.1
    foreach ((array) $this->request->new_preservation_note as $content)
    {
      if (0 < strlen($content))
      {
          $note = new QubitNote;
          $note->content = $content;
          $note->typeId = QubitTerm::PRESERVATION_NOTE_ID;
          $note->userId = $this->context->user->getAttribute('user_id');

          $this->resource->notes[] = $note;
      }
    }
  }
}
