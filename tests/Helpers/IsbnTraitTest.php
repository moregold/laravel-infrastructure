<?php namespace Moregold\Infrastructure\Helpers;

class IsbnTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that it validates an ISBN13 properly
     */
    public function testIsbn13IsValidIsbn13AndInvalidIsbn10()
    {
        $isbn13 = '9780415493529';
        $this->assertTrue(Isbn::isValidIsbn($isbn13), 'Trait incorrectly reporting that '.$isbn13.' is not a valid ISBN');
        $this->assertFalse(Isbn::isValidIsbn10($isbn13), 'Trait incorrectly reporting that '.$isbn13.' is a valid ISBN10');
        $this->assertTrue(Isbn::isValidIsbn13($isbn13), 'Trait incorrectly reporting that '.$isbn13.' is not a valid ISBN13');
    }

    /**
     * Test that it validates an ISBN10 properly
     */
    public function testIsbn10IsInvalidIsbn13AndValidIsbn10()
    {
        $isbn10 = '0415493528';
        $this->assertTrue(Isbn::isValidIsbn($isbn10), 'Trait incorrectly reporting that '.$isbn10.' is not a valid ISBN');
        $this->assertTrue(Isbn::isValidIsbn10($isbn10), 'Trait incorrectly reporting that '.$isbn10.' is not a valid ISBN10');
        $this->assertFalse(Isbn::isValidIsbn13($isbn10), 'Trait incorrectly reporting that '.$isbn10.' is a valid ISBN13');
    }

    /**
     * Test that it validates an ISBN10 with X at the end properly
     */
    public function testIsbn10XIsInvalidIsbn13AndValidIsbn10()
    {
        $isbn10 = '006210618X';
        $this->assertTrue(Isbn::isValidIsbn($isbn10), 'Trait incorrectly reporting that '.$isbn10.' is not a valid ISBN');
        $this->assertTrue(Isbn::isValidIsbn10($isbn10), 'Trait incorrectly reporting that '.$isbn10.' is not a valid ISBN10');
        $this->assertFalse(Isbn::isValidIsbn13($isbn10), 'Trait incorrectly reporting that '.$isbn10.' is a valid ISBN13');
    }

    /**
     * Test that it formats an ISBN13 properly
     */
    public function testIsbn13CanBeFormatted()
    {
        $isbn13unformatted = '978-0-41-549352-9';
        $isbn13formatted = '9780415493529';
        $isbn10formatted = '0415493528';
        $this->assertEquals($isbn13formatted, Isbn::formatIsbn($isbn13unformatted), 'ISBN was not formatted correctly with formatIsbn()');
        $this->assertEquals($isbn13formatted, Isbn::formatIsbn13($isbn13unformatted), 'ISBN13 was not formatted correctly with formatIsbn13()');
        $this->assertEquals($isbn10formatted, Isbn::formatIsbn10($isbn13unformatted), 'ISBN13 was not converted to ISBN10 properly');
    }

    /**
     * Test that it formats an ISBN10 properly
     */
    public function testIsbn10CanBeFormatted()
    {
        $isbn13 = '9780415493529';
        $isbn10 = '0415493528';
        $this->assertEquals($isbn10, Isbn::formatIsbn($isbn10), 'ISBN was not formatted correctly with formatIsbn()');
        $this->assertEquals($isbn13, Isbn::formatIsbn13($isbn10), 'ISBN10 was not converted to ISBN13 properly');
        $this->assertEquals($isbn10, Isbn::formatIsbn10($isbn10), 'ISBN10 was not formatted correctly with formatIsbn10()');
    }

}

class Isbn
{
    use IsbnTrait;
}
