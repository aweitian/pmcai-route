<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 13:32
 */

namespace Aw\Routing;

use Aw\Http\Request;
use Aw\Routing\Dispatch\PmcaiDispatcher;
use Aw\Routing\Matcher\Equal;
use Aw\Routing\Matcher\Mapca;
use Aw\Routing\Matcher\Regexp;
use Aw\Routing\Matcher\StartWith;
use Aw\Routing\UrlGenerator\Pmcai;

class Matcher
{
    const DEFAULT_CONTROL = 'main';
    const DEFAULT_ACTION = 'index';
    const DEFAULT_CONTROL_TPL = '{}Control';
    const DEFAULT_ACTION_TPL = '{}Action';
    public $log = array();
    protected $request;
    protected $matches;
    protected $namespace;
    protected $loc_map = array();
    protected $namespace_map = array();
    protected $ctrl_tpl = '{}Controller';
    protected $act_tpl = '{}Action';

    public function __construct(Request $request, array $config = array())
    {
        $this->request = $request;
        $this->matches = $config;

    }

    /**
     * @return bool
     */
    public function match()
    {
        if (is_null($this->request)) {
            $this->log[] = 'request is null';
            return false;
        }
        if (is_callable($this->matches)) {
            $call = $this->matches;
            return $call($this->request) === true;
        }
        if (isset($this->matches['method'])) {
            $method = explode("|", $this->matches['method']);
            if (!in_array($this->request->getMethod(), $method)) {
                $this->log[] = 'method is not allowed';
                return false;
            }
        }
        if (isset($this->matches['host'])) {
            $host = explode("|", $this->matches['host']);
            if (!in_array($this->request->getHost(), $host)) {
                $this->log[] = 'host is not in white list';
                return false;
            }
        }
        if (!isset($this->matches['type'])) {
            $this->log[] = 'type is required';
            return false;
        }
        if (isset($this->matches['data'])) {
            $data = $this->matches['data'];
        } else {
            $data = array();
        }
        switch ($this->matches['type']) {
            //equal|startwith|regexp|mapca-pmcai|mapca-pmi|mapca-arr
            case 'mapca-pmcai':
                //prefix|mask|loc_map|namespace_map|ctrl_tpl|act_tpl|moduleSkip|type
                $data['type'] = 'pmcai';
                $matcher = new Mapca($data);
                $f = $matcher->match($this->request);
                $this->request->carry['matcher'] = $matcher->matcher;
                /**
                 * @var Pmcai $url
                 */
                $url = $matcher->matcher->getUrl();
                $ca = array('namespace' => $data['namespace']);
                if ($url->getControl()) {
                    $ca['ctl'] = $url->getControl();
                }
                if ($url->getAction()) {
                    $ca['act'] = $url->getAction();
                }
                //ctl_loc|ctl_tpl|act_tpl
                foreach (explode('|', 'ctl_loc|ctl_tpl|act_tpl') as $item) {
                    if (isset($data[$item]))
                        $ca[$item] = $data[$item];
                }
                return $f && PmcaiDispatcher::isDispatchable($ca);
            case 'mapca-pmi':
                $data['type'] = 'pmi';
                $matcher = new Mapca($data);
                $f = $matcher->match($this->request);
                $this->request->carry['matcher'] = $matcher->matcher;
                return $f;
            case "mapca-arr":
                $data['type'] = 'arr';
                $matcher = new Mapca($data);
                $f = $matcher->match($this->request);
                $this->request->carry['matcher'] = $matcher->matcher;
                return $f;
            case "equal":
                $matcher = new Equal($data);
                return $matcher->match($this->request);
            case "startwith":
                $matcher = new StartWith($data);
                return $matcher->match($this->request);
            case "regexp":
                $matcher = new Regexp($data);
                $f = $matcher->match($this->request);
                $this->request->carry['matches'] = $matcher->getMatches();
                return $f;
        }
        return false;
    }
}