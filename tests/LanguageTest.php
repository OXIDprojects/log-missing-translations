<?php

namespace OxidProfessionalServices\LogMissingTranslationsTests;

use PHPUnit\Framework\TestCase;
use OxidProfessionalServices\LogMissingTranslations\Language;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;

class LanguageTest extends TestCase
{
    public function testStillReturnsTranslation(): void
    {
        $language = $this->getMockBuilder(Language::class)
                     ->onlyMethods(['translateStringParentMethod'])
                     ->getMock();
        $language->method('translateStringParentMethod')->willReturn('foo');

        $this->assertEquals('foo', $language->translateString('foo'));
    }

    /**
     * @dataProvider translationDataProvider
     */
    public function testLogsAnErrorIfATranslationWasNotFound(array $filter, int $timesLogMethodIsCalled): void
    {
        $languageIndex = rand(0, 5); // we do not care which language is used
        $adminMode = (bool) (rand(0, 10) % 2); // we do not care if the admin mode is set or not

        $loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);
        $loggerMock->expects($this->exactly($timesLogMethodIsCalled))
            ->method('warning')
            ->with(
                $this->equalTo('translation for NOT_TRANSLATED_STRING not found'),
                [
                    'iLang' => $languageIndex,
                    'blAdminMode' => $adminMode,
                ]
            );

        Registry::set('logger', $loggerMock);
        $config = $this->getMockBuilder(Config::class)
                         ->onlyMethods(['getConfigParam'])
                     ->getMock();
        $config->expects($this->once())
            ->method('getConfigParam')
            ->with($this->equalTo('oxpslogmissingtranslations_filter'))
            ->willReturn($filter);
        Registry::set(Config::class, $config);

        $language = $this->getMockBuilder(Language::class)
                         ->onlyMethods(['translateStringParentMethod', 'isTranslated'])
                         ->getMock();
        $language->method('translateStringParentMethod')->willReturn('NOT_TRANSLATED_STRING');
        $language->method('isTranslated')->willReturn(false);

        $this->assertEquals(
            'NOT_TRANSLATED_STRING',
            $language->translateString('NOT_TRANSLATED_STRING', $languageIndex, $adminMode)
        );
    }

    public function translationDataProvider(): array
    {
        return [
            'filtered translation should not be logged' => [['NOT_TRANSLATED_STRING'], 0],
            'filters should act as wildcards' => [['OT_TRANSLATED_STRIN'], 0],
            'not matching filter should still be logged' => [['I_DO_NOT_MATCH_THE_MISSING_TRANSLATION'], 1],
            'unfiltered translation should not be logged' => [[], 1],
        ];
    }
}
