<?php

declare(strict_types=1);

namespace Tests\ZamboDaniel\SyliusOtpSimplePlugin\DependencyInjection;

use ZamboDaniel\SyliusOtpSimplePlugin\DependencyInjection\ZamboDanielSyliusOtpSimpleExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyDependencyInjectionTest
 */
final class ZamboDanielSyliusOtpSimpleExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new ZamboDanielSyliusOtpSimpleExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'option' => 'option_value',
        ];
    }

    /**
     * @test
     */
    public function after_loading_the_correct_parameter_has_been_set(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('zambo_daniel_sylius_otp_simple.option', 'option_value');
    }
}
