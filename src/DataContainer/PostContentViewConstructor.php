<?php
/*
 * Language relations for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2018
 * @package    contao-languagerelation
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */
 
namespace Agoat\LanguageRelationBundle\DataContainer;

use Agoat\LanguageRelationBundle\LanguageRelation\LanguageRelationGenerator;
use Contao\Backend;


/**
 * Add Language relation widget field
 */
class PostContentViewConstructor extends AbstractConstructor
{

	public function buildDca ()
	{

		$GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'][] = function(\DataContainer $dc) {
			if ('posts' != $_GET['do'] || ($_GET['act'] && 'select' != $_GET['act'])) {
				return;
			}

			if ('copy' == $_GET['mode'] || 'cut' == $_GET['mode']) {
				$content = \ContentModel::findByPk($dc->id);
				$id = $content->pid;
			} else {
				$id = $dc->id;
			}
			
			$current = \PostsModel::findByPk($dc->id);
			$relatedPosts = $this->getRelated($current);

			if (null === $relatedPosts) {
				$parent = \ArchiveModel::findByPk($current->pid);
				$relatedArchives = $this->getRelated($parent);
			}
			
			if (null === $relatedArchives) {
				$parent = \PageModel::findByPk($parent->pid);
				$relatedPages = $this->getRelated($parent);
			}
			
			$related = array();
			
			if (null !== $relatedPosts) {
				foreach ($relatedPosts as $language=>$article) {
					$related[$language] = [
						'editUrl' => Backend::addToUrl('id='.$article->id),
						'title' => $article->pageTitle ?: $article->title
					];
				}
			} elseif (null !== $relatedArchives) {
				foreach ($relatedArchives as $language=>$page) {
					$related[$language] = [
						'editUrl' => Backend::addToUrl('id='.$page->id.'&table=tl_posts'),
						'title' => $page->pageTitle ?: $page->title
					];
				}
			} elseif (null !== $relatedPages) {
				foreach ($relatedPages as $language=>$page) {
					$related[$language] = [
						'editUrl' => Backend::addToUrl('pn='.$page->id, true, ['table']),
						'title' => $page->pageTitle ?: $page->title
					];
				}
			}

			$this->createRelationButton($related, $current);
			
			dump($GLOBALS['TL_DCA'][$this->table]['list']);		

		};

	}
}
