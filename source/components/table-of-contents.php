<?php
/**
 * Table of Contents
 * 
 * @param array $items
 */
?>

<nav <?= $htmlAttributesString([
    'class'     => 'table-of-contents',
    'itemscope' => true,
    'itemtype'  => 'http://schema.org/PublicationIssue',
]) ?>>
    <div class="table-of-contents__inner">
        <h2 class="table-of-contents__title">
            Table of Contents
        </h2>
        <div class="table-of-contents__list-wrapper">
            <ol class="table-of-contents__list">
                <?php foreach ($items ?? [] as $key => $header) : ?>
                    <li class="table-of-contents__item <?= $key == 0 ? '_active' : '' ?>" data-anchor="<?= $header['id'] ?>"
                        itemprop="hasPart" itemscope itemtype="http://schema.org/Article">
                        <a href="#<?= $header['id'] ?>" class="table-of-contents__item-link">
                            <?= $header['title'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</nav>