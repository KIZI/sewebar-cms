<?php
  // no direct access
  defined('_JEXEC') or die;

  JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');



  // Create shortcuts to some parameters.
  $params		= $this->item->params;
  $canEdit	= $this->item->params->get('access-edit');

  echo '<article class="item-page item-page'.$this->pageclass_sfx.'">';

  echo '<header>';
    //ikonky pro tisk, editaci atp.
    if ($canEdit ||  $params->get('show_print_icon') || $params->get('show_email_icon')) {
      echo '<div class="buttonheading">';
      if (!$this->print){
        if ($params->get('show_print_icon')){
          echo '<div class="print">';
          echo JHtml::_('icon.print_popup',  $this->item, $params);
          echo '</div>';
        }
      }
      if ($params->get('show_email_icon')){
        echo '<div class="email">';
        echo JHtml::_('icon.email',  $this->item, $params);
        echo '</div>';
      }
      if ($canEdit){
        echo '<div class="edit">';
        echo JHtml::_('icon.edit', $this->item, $params);
        echo '</div>';
      }
      echo '</div>';
    }
    //--ikonky pro tisk, editaci atp.

    //nadpis stránky
    if ($this->params->get('show_page_heading', 1)){
      //máme stránku i se záhlavím
      echo '<hgroup>';
      echo '<h1>'.$this->escape($this->params->get('page_heading')).'</h1>';
      echo '<h2>';
      if ($params->get('show_title')|| $params->get('access-edit')){
        if ($params->get('link_titles') && !empty($this->item->readmore_link)) {
          echo '<a href="'.$this->item->readmore_link.'">'.$this->escape($this->item->title).'</a>';
        } else {
          echo $this->escape($this->item->title);
        }
      }
      echo '</h2>';
      echo '</hgroup>';
    }else{
      //máme stránku bez záhlaví
      echo '<h1>';
      if ($params->get('show_title')|| $params->get('access-edit')){
        if ($params->get('link_titles') && !empty($this->item->readmore_link)) {
          echo '<a href="'.$this->item->readmore_link.'">'.$this->escape($this->item->title).'</a>';
        } else {
          echo $this->escape($this->item->title);
        }
      }
      echo '</h1>';
    }
    //nadpis stránky

    //zobrazení odkazů na kategorie
    if (($params->get('show_parent_category') && $this->item->parent_slug != '1:root')||$params->get('show_category')){
      echo '<div class="iteminfo">';
      if ($params->get('show_parent_category') && $this->item->parent_slug != '1:root'){
        echo '<span class="category">';
        $title = $this->escape($this->item->parent_title);
        $url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$title.'</a>';
        if ($params->get('link_parent_category') AND $this->item->parent_slug){
          echo JText::sprintf('COM_CONTENT_PARENT', $url);
        } else {
          echo JText::sprintf('COM_CONTENT_PARENT', $title);
        }
        echo '</span>';

      }

      if ($params->get('show_category')){
        echo '<span class="sub-category">';
        $title = $this->escape($this->item->category_title);
        $url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';
        if ($params->get('link_category') and $this->item->catslug){
          echo JText::sprintf('COM_CONTENT_CATEGORY', $url);
        } else {
          echo JText::sprintf('COM_CONTENT_CATEGORY', $title);
        }
        echo '</span>';
      }
      echo '</div>';
    }
    //--zobrazení odkazů na kategorie
  echo '</header>';

  //zobrazení samotného obsahu článku
  if (!$params->get('show_intro')){
    echo $this->item->event->afterDisplayTitle;
  }

  echo $this->item->event->beforeDisplayContent;

  if (isset ($this->item->toc)){
    echo $this->item->toc;
  }

  echo $this->item->text;

  //zobrazení detailů o vytvoření článku
  if ($params->get('show_create_date')||$params->get('show_modify_date')||$params->get('show_publish_date')||($params->get('show_author') && !empty($this->item->author ))){
    echo '<footer>';

    if ($params->get('show_create_date')){
      echo '<span class="create">';
      echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC1')));
      echo '</span>';
    }

    if ($params->get('show_modify_date')){
      echo '<span class="modified">';
      echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC1')));
      echo '</span>';
    }

    if ($params->get('show_publish_date')){
      echo '<span class="published">';
      echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE', JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC1')));
      echo '</span>';
    }

    if ($params->get('show_author') && !empty($this->item->author )){
      echo ' <span class="createdby">';
      if (!empty($this->item->created_by_alias)){
        $author=$this->item->created_by_alias;
      }else{
        $author=$this->item->author;
      }

      if (!empty($this->item->contactid ) &&  $params->get('link_author') == true){
        echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
          JHTML::_('link',JRoute::_('index.php?option=com_contact&view=contact&id='.$this->item->contactid),$author));
      }else{
        echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author);
      }
      echo '</span>';
    }

    echo '</footer>';
  }
  //--zobrazení detailů o vytvoření článku

  echo $this->item->event->afterDisplayContent;
  //--zobrazení samotného obsahu článku

  echo '</article>';


