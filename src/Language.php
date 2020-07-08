<?php

namespace OxidProfessionalServices\LogMissingTranslations;

use OxidEsales\Eshop\Core\Registry;

class Language extends Language_parent
{
    /**
     * This method is only used to mock the value in the unit test
     *
     * @codeCoverageIgnore
     *
     * @param int|null $iLang
     * @param bool|null $blAdminMode
     *
     * @return string|array
     */
    public function translateStringParentMethod(
        string $sStringToTranslate,
        ?int $iLang = null,
        ?bool $blAdminMode = null
    ) {
        return parent::translateString($sStringToTranslate, $iLang, $blAdminMode);
    }

    /**
     * @return string|array
     */
    public function translateString($sStringToTranslate, $iLang = null, $blAdminMode = null)
    {
        $translation = $this->translateStringParentMethod($sStringToTranslate, $iLang, $blAdminMode);

        if (!$this->isTranslated() && !$this->isFiltered($sStringToTranslate)) {
            Registry::getLogger()->warning(
                "translation for $sStringToTranslate not found",
                ['iLang' => $iLang, 'blAdminMode' => $blAdminMode]
            );
        }

        return $translation;
    }

    private function isFiltered(string $sStringToTranslate): bool
    {
        /** @var array<string> $filters */
        $filters = Registry::getConfig()->getConfigParam('oxpslogmissingtranslations_filter');
        foreach ($filters as $filter) {
            if (strpos($sStringToTranslate, $filter) !== false) {
                return true;
            }
        }

        return false;
    }
}
