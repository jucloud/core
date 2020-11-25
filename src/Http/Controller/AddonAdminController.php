<?php
namespace JuCloud\Core\Http\Controller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Admin;

class AddonAdminController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct() {
        $this->getSidebarMenu();
    }

    /**
     * 获取组件名称
     * @Author    王凯
     * @DateTime  2020-11-23
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]
     */
    public function getAddonTitle() {

        $config = json_decode(app()->make(Filesystem::class)->get(base_path('addons/' . ucwords($this->getAddonName())) . '/config.json'), true);

        return $config['title'];
    }

    /**
     * 获取组件名称
     * @Author    王凯
     * @DateTime  2020-11-23
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]
     */
    public function getAddonName() {

        $uris = explode('/addons/', request()->url())[1];

        $addonName = ucwords(substr($uris, 0, strpos($uris, "/")));
        return $addonName;
    }

    /**
     * 获取左侧菜单
     * @Author    王凯
     * @DateTime  2020-11-23
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]
     */
    public function getSidebarMenu() {
        
        $config = json_decode(app()->make(Filesystem::class)->get(base_path('addons/' . ucwords($this->getAddonName())) . '/config.json'), true);
        
        admin_inject_section(Admin::SECTION['LEFT_SIDEBAR_MENU'], function () use ($config) {

            $builder = Admin::menu();
            $adminUrl = admin_url('/');

            $html = <<<EOD

<li class="nav-item">
    <a onclick="window.location.href='{$adminUrl}'" href="javascript:;" class="nav-link">
        <i class="fa fa-angle-left"></i><p>返回控台</p>
    </a>
</li>

EOD;

            foreach (Helper::buildNestedArray($config['menu']) as $item) {
                $html .= view('admin::partials.menu', ['item' => $item, 'builder' => $builder])->render();
            }
            return $html;
        });
    }
}
