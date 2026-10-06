<?php

declare(strict_types=1);

namespace Drupal\Tests\dsfr_editorial\Unit;

use Drupal\dsfr_editorial\Plugin\Filter\DsfrTable;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the DSFR table markup filter.
 *
 * @coversDefaultClass \Drupal\dsfr_editorial\Plugin\Filter\DsfrTable
 * @group dsfr_editorial
 */
final class DsfrTableTest extends UnitTestCase {

  /**
   * Runs the filter on a fragment.
   */
  private function filter(string $html): string {
    $filter = new DsfrTable([], 'dsfr_editorial_table', ['provider' => 'dsfr_editorial']);
    return (string) $filter->process($html, 'en')->getProcessedText();
  }

  /**
   * Text without a table is returned unchanged.
   *
   * @covers ::process
   */
  public function testTextWithoutTable(): void {
    $this->assertSame('<p>No table here.</p>', $this->filter('<p>No table here.</p>'));
  }

  /**
   * Tables get the four DSFR containers and header scopes.
   *
   * @covers ::process
   */
  public function testWrapAndScopes(): void {
    $result = $this->filter('<table><thead><tr><th>A</th><th>B</th></tr></thead><tbody><tr><th>Row</th><td>1</td></tr></tbody></table>');
    $this->assertStringContainsString('<div class="fr-table"><div class="fr-table__wrapper"><div class="fr-table__container"><div class="fr-table__content"><table>', $result);
    $this->assertSame(2, substr_count($result, 'scope="col"'));
    $this->assertSame(1, substr_count($result, 'scope="row"'));
  }

  /**
   * Existing scopes and ids are kept, and filtering twice changes nothing.
   *
   * @covers ::process
   */
  public function testIdempotentAndRespectsAuthoring(): void {
    $once = $this->filter('<table><tbody><tr><th scope="col" id="h1">A</th></tr></tbody></table>');
    $this->assertStringContainsString('<th scope="col" id="h1">', $once);
    $this->assertSame($once, $this->filter($once));
  }

}
