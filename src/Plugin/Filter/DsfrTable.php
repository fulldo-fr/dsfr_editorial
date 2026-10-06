<?php

declare(strict_types=1);

namespace Drupal\dsfr_editorial\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Gives tables written in the editor the DSFR table markup.
 *
 * Two things the editor does not do, and an editor should not have to:
 *
 * - The wrapper. The DSFR table component expects four nested containers
 *   around the table element; without them the table gets neither its style
 *   nor its horizontal scrolling on small screens.
 * - Header scopes. Accessibility guidelines (RGAA 5.7, WCAG 1.3.1) require
 *   each header cell to declare what it applies to. The editor produces bare
 *   th elements: a header cell in the header row covers its column, a header
 *   cell at the start of a body row covers its row. The rule is deterministic,
 *   so it is applied here.
 *
 * This filter must run AFTER "Limit allowed HTML", otherwise the scope
 * attributes and the containers it adds would be stripped.
 */
#[Filter(
  id: 'dsfr_editorial_table',
  title: new TranslatableMarkup('DSFR table markup'),
  description: new TranslatableMarkup('Wraps tables in the DSFR table containers and adds the scope of header cells.'),
  type: FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
  weight: 20,
)]
final class DsfrTable extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    if (!str_contains($text, '<table')) {
      return new FilterProcessResult($text);
    }
    $document = Html::load($text);
    foreach (iterator_to_array($document->getElementsByTagName('table')) as $table) {
      self::addScopes($table);
      self::wrap($document, $table);
    }
    return new FilterProcessResult(Html::serialize($document));
  }

  /**
   * Adds a scope to every header cell that has neither a scope nor an id.
   */
  private static function addScopes(\DOMElement $table): void {
    foreach (iterator_to_array($table->getElementsByTagName('th')) as $header) {
      if ($header->hasAttribute('scope') || $header->hasAttribute('id')) {
        continue;
      }
      $group = $header->parentNode?->parentNode;
      $inHead = $group instanceof \DOMElement && $group->tagName === 'thead';
      $header->setAttribute('scope', $inHead ? 'col' : 'row');
    }
  }

  /**
   * Places the table inside the DSFR containers, once.
   */
  private static function wrap(\DOMDocument $document, \DOMElement $table): void {
    $parent = $table->parentNode;
    if ($parent instanceof \DOMElement && str_contains(' ' . $parent->getAttribute('class') . ' ', ' fr-table__content ')) {
      return;
    }
    $outer = $document->createElement('div');
    $outer->setAttribute('class', 'fr-table');
    $current = $outer;
    foreach (['fr-table__wrapper', 'fr-table__container', 'fr-table__content'] as $class) {
      $level = $document->createElement('div');
      $level->setAttribute('class', $class);
      $current->appendChild($level);
      $current = $level;
    }
    $parent->replaceChild($outer, $table);
    $current->appendChild($table);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('Tables get the DSFR markup; header cells in the first row apply to their column, header cells starting a row apply to their row.');
  }

}
