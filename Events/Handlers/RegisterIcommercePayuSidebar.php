<?php

namespace Modules\IcommercePayu\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterIcommercePayuSidebar implements \Maatwebsite\Sidebar\SidebarExtender
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     *
     * @internal param Guard $guard
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    public function handle(BuildingSidebar $sidebar)
    {
        $sidebar->add($this->extendWith($sidebar->getMenu()));
    }

    /**
     * @param Menu $menu
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        /*
        $menu->group(trans('core::sidebar.content'), function (Group $group) {
            $group->item(trans('icommercepayu::icommercepayus.title.icommercepayus'), function (Item $item) {
                $item->icon('fa fa-copy');
                $item->weight(10);
                $item->authorize(
                    
                );
                $item->item(trans('icommercepayu::payuconfigs.title.payuconfigs'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.icommercepayu.payuconfig.create');
                    $item->route('admin.icommercepayu.payuconfig.index');
                    $item->authorize(
                        $this->auth->hasAccess('icommercepayu.payuconfigs.index')
                    );
                });
// append

            });
        });
*/

        return $menu;
    }
}
