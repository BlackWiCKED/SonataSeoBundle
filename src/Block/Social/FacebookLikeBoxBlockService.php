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
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Facebook like box integration.
 *
 * @see https://developers.facebook.com/docs/plugins/like-box-for-pages/
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 *
 * @deprecated since sonata-project/seo-bundle 2.14, to be removed in 3.0.
 */
class FacebookLikeBoxBlockService extends BaseFacebookSocialPluginsBlockService
{
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => '@SonataSeo/Block/block_facebook_like_box.html.twig',
            'url' => null,
            'width' => null,
            'height' => null,
            'colorscheme' => $this->colorschemeList['light'],
            'show_faces' => true,
            'show_header' => true,
            'show_posts' => false,
            'show_border' => true,
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
                ['width', IntegerType::class, [
                    'required' => false,
                    'label' => 'form.label_width',
                ]],
                ['height', IntegerType::class, [
                    'required' => false,
                    'label' => 'form.label_height',
                ]],
                ['colorscheme', ChoiceType::class, [
                    'required' => true,
                    'choices' => $this->colorschemeList,
                    'label' => 'form.label_colorscheme',
                ]],
                ['show_faces', CheckboxType::class, [
                    'required' => false,
                    'label' => 'form.label_show_faces',
                ]],
                ['show_header', CheckboxType::class, [
                    'required' => false,
                    'label' => 'form.label_show_header',
                ]],
                ['show_posts', CheckboxType::class, [
                    'required' => false,
                    'label' => 'form.label_show_posts',
                ]],
                ['show_border', CheckboxType::class, [
                    'required' => false,
                    'label' => 'form.label_show_border',
                ]],
            ],
            'translation_domain' => 'SonataSeoBundle',
        ]);
    }

    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (null !== $code ? $code : $this->getName()), false, 'SonataSeoBundle', [
            'class' => 'fa fa-facebook-official',
        ]);
    }
}
