<div class="footer-nav-area" id="footerNav">
    <div class="newsten-footer-nav h-100">
        <ul class="h-100 d-flex align-items-center justify-content-between">
            <?php $segments = explode('/', rtrim($uri, '/'));
            $lastSegment = end($segments);
            ?>
            <li class="<?php if ($lastSegment == 'Home') echo 'active';  ?>">
                <a href="./Home">
                    <i class="lni lni-home"></i>
                </a>
            </li>
            <li class="<?php if ($lastSegment == 'Chat') echo 'active';  ?>">
                <a href="./Chat">
                    <i class="lni lni-support"></i>
                </a>
            </li>
            <li class="<?php if ($lastSegment == 'Redeem') echo 'active';  ?>">
                <a href="./Redeem">
                    <i class="lni lni-revenue"></i>
                </a>
            </li>
            <li class="<?php if ($lastSegment == 'Offers') echo 'active';  ?>">
                <a href="./Offers">
                    <i class="lni lni-gift"></i>
                </a>
            </li>
            <li class="<?php if ($lastSegment == 'Profile') echo 'active';  ?>">
                <a href="./Profile">
                    <i class="lni lni-user"></i>
                </a>
            </li>
        </ul>
    </div>
</div>