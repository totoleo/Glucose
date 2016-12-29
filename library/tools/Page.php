<?php
namespace tools;

class Page {
    protected $pageTotal;
    protected $page;
    protected $total;
    protected $size;
    protected $baseUrl = '';
    public $option;

    public function __construct() {
        $this->option = [
            'size'   => 8, // 选页列表按钮个数
            'topage' => false, // 是否显示跳页按钮
            'simple' => false, // 只打印简略信息
            'page'   => 'page', // 跳页参数名称
            'filter' => ['s'], // 过滤传递参数
        ];
    }

    public function setTotal($total) {
        $this->total = $total;
        return $this;
    }

    public function setCurrentPage($page) {
        $this->page = $page;
        return $this;
    }

    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    public function getPageTotal() {
        $this->pageTotal = $this->total % $this->size == 0 ? intval($this->total / $this->size) 
            : intval($this->total / $this->size) + 1;
        return $this->pageTotal;
    }

    protected function isPrev() {
        $htSocure = '<li class="disabled"><a>«</a></li>';
        if ($this->page > 1)
            $htSocure = '<li><a title="上一页" href="' . str_replace('{page}', ($this->page - 1), $this->assembleUrl()) . '">«</a></li>';
        return $htSocure;
    }
    
    protected function isNext() {
        $htSocure = '<li class="disabled"><a>»</a></li>';
        if ($this->page < $this->pageTotal)
            $htSocure = '<li><a title="下一页" href="' . str_replace('{page}', ($this->page + 1), $this->assembleUrl()) . '">»</a></li>';
        return $htSocure;
    }
    
    protected function assembleUrl() {
        $url = $this->baseUrl . '?';
        $page = $this->option['page'];
        $filter = $this->option['filter'];
        foreach ($_GET as $key => $val) {
            if ($page == $key) continue;
            if (in_array($key, $filter)) continue;
            $url .= $key . '=' . $val . '&';
        }
        $url .= $page . '={page}';
        return $url;
    }
    
    public function printToSmallPage() {
        $this->getPageTotal();
        // < 1/100 >
        // 最多显示多少个页码
        $_pageNum = $this->option['size'];
        $page  = $this->page;
        $pages = $this->pageTotal;
        $url   = $this->assembleUrl();
        // 当前页面小于1 则为1
        $page = $page < 1 ? 1 : $page;
        // 当前页大于总页数 则为总页数
        $page = $page > $pages ? $pages : $page;
        // 页数小当前页 则为当前页
        $pages = $pages < $page ? $page : $pages;
        
        // 设置分页模板
        $_html = '';
        if ($this->pageTotal > 1) {
            $_html  = '<div class="pager"><ul>';
            $_html .= '<li><a class="pager-link" {prevUrl} title="上一页"><span class="icon {prevClass}"></span></a></li>';
            $_html .= '<li><span class="current">{current}</span>/{total}</li>';
            $_html .= '<li><a class="pager-link" {nextUrl} title="下一页"><span class="icon {nextClass}"></span></a></li></ul>';
            $_html .= '</div>';
            
            $prevUrl = 'href="' . str_replace('{page}', $page - 1, $url) . '"';
            $prevClass = 'icon-btn-prev-2';
            if ($page <= 1) {
                $prevUrl = '';
                $prevClass .= '-disable';
            }
            
            $nextUrl = 'href="' . str_replace('{page}', $page + 1, $url) . '"';
            $nextClass = 'icon-btn-next-2';
            if ($page >= $pages) {
                $nextUrl = '';
                $nextClass .= '-disable';
            }
            
            $_html = str_replace('{prevUrl}',   $prevUrl,   $_html);
            $_html = str_replace('{prevClass}', $prevClass, $_html);
            $_html = str_replace('{nextUrl}',   $nextUrl,   $_html);
            $_html = str_replace('{nextClass}', $nextClass, $_html);
            $_html = str_replace('{current}',   $page,      $_html);
            $_html = str_replace('{total}',     $pages,     $_html);
        }
        return $_html;
    }
    
    public function printToPage() {
        $this->getPageTotal();
        //最多显示多少个页码
        $_pageNum = $this->option['size'];
        $page  = $this->page;
        $pages = $this->pageTotal;
        $url   = $this->assembleUrl();
        //当前页面小于1 则为1
        $page = $page < 1 ? 1 : $page;
        //当前页大于总页数 则为总页数
        $page = $page > $pages ? $pages : $page;
        //页数小当前页 则为当前页
        $pages = $pages < $page ? $page : $pages;
    
        //计算开始页
        $_start = $page - floor($_pageNum / 2);
        $_start = $_start<1 ? 1 : $_start;
        //计算结束页
        $_end = $page + floor($_pageNum / 2);
        $_end = $_end>$pages? $pages : $_end;
    
        //当前显示的页码个数不够最大页码数，在进行左右调整
        $_curPageNum = $_end-$_start+1;
        //左调整
        if ($_curPageNum < $_pageNum && $_start > 1) {
            $_start = $_start - ($_pageNum - $_curPageNum);
            $_start = $_start < 1 ? 1 : $_start;
            $_curPageNum = $_end - $_start+1;
        }
        //右边调整
        if ($_curPageNum < $_pageNum && $_end < $pages) {
            $_end = $_end + ($_pageNum - $_curPageNum);
            $_end = $_end>$pages ? $pages : $_end;
        }
        
        $_pageHtml = '';
        if ($this->pageTotal > 1) {
            $_pageHtml  = '<div class="page-link">';
            $_pageHtml .= '<ul class="page-link-ul">';
            $_pageHtml .= $this->isPrev();
            for ($i = $_start; $i <= $_end; $i++) {
                if ($i == $page)
                    $_pageHtml .= '<li class="current"><a>' . $i . '</a></li>';
                else
                    $_pageHtml .= '<li><a href="'. str_replace('{page}', $i, $url) . '">' . $i . '</a></li>';
            }
            $_pageHtml .= $this->isNext();
            
            $_pageHtml .= '</ul>';
            $_pageHtml .= '<span>' . $this->page . '/' . $this->pageTotal . '</span>';
            // $_pageHtml .= '<span>共' . $this->pageTotal . '页</span>';
            $_pageHtml .= '</div>';
        }
        return $_pageHtml;
    }
    
    public function printToStyle($date = '') {
        return '<link rel="stylesheet" href="/css/common/page-link.css?t=' . ($date ? $date : date('Ymd')) . '" />';
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }
}