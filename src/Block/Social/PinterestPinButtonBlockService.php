<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\SeoBundle\Block\Social;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Pinterest pin button integration.
 *
 * @see http://fr.business.pinterest.com/widget-builder/#do_pin_it_button
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 *
 * @deprecated since sonata-project/seo-bundle 2.14, to be removed in 3.0.
 */
class PinterestPinButtonBlockService extends AbstractAdminBlockService
{
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => '@SonataSeo/Block/block_pinterest_pin_button.html.twig',
            'size' => null,
            'shape' => null,
            'url' => null,
            'image' => null,
            'description' => null,
        ]);
    }

    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        $form->add('settings', ImmutableArrayType::class, [
            'keys' => [
                ['url', UrlType::class, [
                    'required' => false,
                    'label' => 'form.label_url',
                ]],
                ['image', TextType::class, [
                    'required' => false,
                    'label' => 'form.label_image',
                ]],
                ['description', TextType::class, [
                    'required' => false,
                    'label' => 'form.label_description',
                ]],
                ['size', IntegerType::class, [
                    'required' => false,
                    'label' => 'form.label_size',
                ]],
                ['shape', ChoiceType::class, [
                    'required' => false,
                    'choices' => [
                        'rectangular' => 'form.label_shape_rectangular',
                        'round' => 'form.label_shape_round',
                    ],
                    'label' => 'form.label_shape',
                ]],
            ],
            'translation_domain' => 'SonataSeoBundle',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null)
    {
        $block = $blockContext->getBlock();
        $settings = array_merge($blockContext->getSettings(), $block->getSettings());

        return $this->renderResponse($blockContext->getTemplate(), [
            'block' => $blockContext->getBlock(),
            'settings' => $settings,
        ], $response);
    }

    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (null !== $code ? $code : $this->getName()), false, 'SonataSeoBundle', [
            'class' => 'fa fa-pinterest-p',
        ]);
    }
}
