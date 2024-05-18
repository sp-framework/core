<?php

namespace Apps\Core\Packages\Adminltetags\Tags;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Card extends Adminltetags
{
    protected $params;

    protected $content = '';

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        if (isset($this->params['cardId'])) {
            $hasCardId = $this->params['cardId'];
        } else if (isset($this->params['componentId']) && isset($this->params['sectionId'])) {
            $hasCardId = $this->params['componentId'] . '-' . $this->params['sectionId'] . '-card';
        } else if (!isset($this->params['componentId']) && isset($this->params['sectionId'])) {
            $hasCardId = $this->params['sectionId'] . '-card';
        } else {
            $hasCardId = null;
        }

        if (!$hasCardId) {
            $this->content .= '<span class="text-uppercase text-danger">ERROR: cardId missing</span>';
            return;
        }

        isset($this->params['cardType']) ?
        $cardType = "bg-" . $this->params['cardType'] :
        $cardType = "bg-primary";

        isset($this->params['cardHeaderAdditionalClass']) ?
        $cardHeaderAdditionalClass = $this->params['cardHeaderAdditionalClass'] :
        $cardHeaderAdditionalClass = '';

        // cardCollapsed : if configured, card will start as collapsed
        isset($this->params['cardCollapsed']) && $this->params['cardCollapsed'] === true ?
        $hasCardCollapsed = "collapsed-card" :
        $hasCardCollapsed = "";

        isset($this->params['cardRefreshSource']) ?
        $cardRefreshSource = "data-source=" . $this->params['cardRefreshSource'] :
        $cardRefreshSource = "";

        isset($this->params['cardAnimationSpeed']) ?
        $cardAnimationSpeed = "data-animation-speed=" . $cardAnimationSpeed :
        $cardAnimationSpeed = "data-animation-speed=300";

        isset($this->params['cardRefreshParams']) ?
        $cardRefreshParams = "data-params=" . $this->params['cardRefreshParams'] :
        $cardRefreshParams = "";

        isset($this->params['cardRefreshDataType']) ?
        $cardRefreshDataType = "data-datatype=" . $this->params['cardRefreshDataType'] :
        $cardRefreshDataType = "";

        isset($this->params['cardRefreshMethod']) ?
        $cardRefreshMethod = "data-method=" . $this->params['cardRefreshMethod'] :
        $cardRefreshMethod = "";

        isset($this->params['cardRefreshSourceSelector']) ?
        $cardRefreshSourceSelector =
            "data-sourceselector=" . $this->params['cardRefreshSourceSelector'] :
        $cardRefreshSourceSelector = "";

        isset($this->params['cardAdditionalClass']) ?
        $cardAdditionalClass = $this->params['cardAdditionalClass'] :
        $cardAdditionalClass = "";

        isset($this->params['cardIconAdditionalClass']) ?
        $cardIconAdditionalClass = $this->params['cardIconAdditionalClass'] :
        $cardIconAdditionalClass = '';

        isset($this->params['cardIcon']) ?
            $cardIcon =
                '<span class="widget-icon">
                    <i class="mr-1 fas fa-fw fa-' . $this->params['cardIcon'] . ' ' .
                     $cardIconAdditionalClass .
                    '"></i></span>' :
            $cardIcon = '';

        isset($this->params['cardTitle']) ?
        $cardTitle = strtoupper($this->params['cardTitle']) :
        $cardTitle = 'MISSING TITLE';

        isset($this->params['cardSpanType']) && isset($this->params['cardSpanText']) ?
        $cardSpan =
            '<span class="badge bg-' . $this->params['cardSpanType'] . '">' .
                $this->params['cardSpanText'] . '</span>' :
        $cardSpan = '';

        isset($this->params['cardCollapsed']) && $this->params['cardCollapsed'] === true ?
        $cardCollapsed =
            '<button type="button" class="btn btn-tool" data-card-widget="collapse">' .
            '<i class="fas fa-fw fa-plus"></i></button>' :
        $cardCollapsed = '';

        if (isset($this->params['cardWidgetMode']) && $this->params['cardWidgetMode'] === true) {
            $bodyIsWidget = ' style="padding: 5px !important;"';
            $cardIsWidget = ' style="box-shadow: 0 0 0 0;margin: 0;border: 0 !important"';
            $headerIsWidget = ' style="position: absolute;width: 100%;z-index:99;opacity: 0.8;"';
            $cardHeaderHidden = 'hidden';
        } else {
            $bodyIsWidget = '';
            $cardIsWidget = '';
            $headerIsWidget = '';
            $cardHeaderHidden = '';
        }
        if (isset($this->params['cardWidgetRoot']) && $this->params['cardWidgetRoot'] === true) {
            $bodyIsWidget = ' style="padding: 0px !important;"';
            $cardIsWidget = ' style="box-shadow: 0 0 0 0;margin: 0;background-color: #f4f6f9;"';
        }

        $tools = '';
        if (isset($this->params['cardShowTools']) && count($this->params['cardShowTools']) > 0) {
            foreach ($this->params['cardShowTools'] as $key => $tool) {
                if ($tool === "refresh") {
                    $tools .=
                        '<button type="button" class="btn btn-tool btn-tool-refresh" data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Refresh" data-card-widget="refresh"' . $cardRefreshSource . ' ' . $cardRefreshParams . ' ' . $cardRefreshDataType . ' ' . $cardRefreshMethod . ' ' . $cardRefreshSourceSelector . '>
                            <i class="fas fa-fw fa-sync-alt"></i>
                        </button>';
                } else if ($tool === "maximize") {
                    $tools .=
                        '<button type="button" class="btn btn-tool btn-tool-maximize" data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Maximize" data-card-widget="maximize">
                            <i class="fas fa-fw fa-expand"></i>
                        </button>';
                } else if ($tool === "collapse") {
                    if (!isset($this->params['cardCollapsed']) ||
                        (isset($this->params['cardCollapsed']) && $this->params['cardCollapsed'] === false)
                    ) {
                        $tools .=
                            '<button type="button" class="btn btn-tool btn-tool-collapse" ' . $cardAnimationSpeed . ' data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Collapse" data-card-widget="collapse">
                                <i class="fas fa-fw fa-minus"></i>
                            </button>';
                    }
                } else if ($tool === "settings") {
                    $tools .=
                        '<button type="button" class="btn btn-tool btn-tool-settings" data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Settings" data-card-widget="settings">
                            <i class="fas fa-fw fa-gear"></i>
                        </button>';
                } else if ($tool === "move") {
                    $tools .=
                        '<button type="button" class="btn btn-tool btn-tool-move" data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Move" data-card-widget="move">
                            <i class="fas fa-fw fa-up-down-left-right"></i>
                        </button>';
                } else if ($tool === "remove") {
                    $tools .=
                        '<button type="button" class="btn btn-tool btn-tool-remove" ' . $cardAnimationSpeed . ' data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Remove" data-card-widget="remove">
                            <i class="fas fa-fw fa-times"></i>
                        </button>';
                } else if ($tool === "widgetRemove") {
                    $tools .=
                        '<button type="button" class="btn btn-tool btn-tool-widgetRemove" data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Remove" data-card-widget="widgetRemove">
                            <i class="fas fa-fw fa-times"></i>
                        </button>';
                }
            }
        } else {
            $tools = '';
        }

        if ($this->view->canMsv && $this->view->usedModules) {
            $url = $this->links->url($this->params['component']['route'] . '/q/settings/true');
            $tools .=
                '<a href="' . $url . '" class="btn btn-tool btn-tool-package-settings-link disabled" role="button" hidden="">Package Settings Link</a>' .
                '<script>
                    $(document).ready(function() {
                        $(".btn-tool-package-settings").click(function() {
                            BazContentLoader.loadAjax($(".btn-tool-package-settings-link"), {
                                ajaxBefore                      : function () {
                                                                    Pace.restart();
                                                                    $("#baz-content").empty();
                                                                    $("#loader").attr("hidden", false);
                                                                },
                                ajaxFinished                    : function () {
                                                                    BazCore.updateBreadcrumb();
                                                                    $("#loader").attr("hidden", true);
                                                                    $(".tooltip").remove();
                                                                },
                                ajaxError                       : function () {
                                                                    $("#loader").attr("hidden", true);
                                                                    BazCore.updateBreadcrumb();
                                                                }
                            });
                            BazContentLoader.init();
                        });
                    });
                </script>
                <button type="button" class="btn btn-tool btn-tool-package-settings" data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Package Settings" data-card-widget="package-settings">
                    <i class="fas fa-fw fa-gears"></i>
                </button>';
        }

        if ($this->config->cache->enabled) {
            $tools .=
                '<button type="button" class="btn btn-tool btn-tool-reset-cache" data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="Reset Cache">
                    <i class="fas fa-fw fa-database"></i>
                </button>';
        }

        $cardBodyAdditionalClass =
            isset($this->params['cardBodyAdditionalClass']) ?
            $this->params['cardBodyAdditionalClass'] :
            '';

        if (isset($this->params['cardBodyContent'])) {

            $cardBody = $this->params['cardBodyContent'];

        } else if (isset($this->params['cardBodyInclude']) &&
                   isset($this->params['cardBodyIncludeParams'])
        ) {

            $cardBody =
                $this->view->getPartial(
                    $this->params['cardBodyInclude'],
                    $this->params['cardBodyIncludeParams']
                );
        } else if (isset($this->params['cardBodyInclude'])) {

            $cardBody =
                $this->view->getPartial(
                    $this->params['cardBodyInclude'],
                    $this->params
                );
        } else {
            $cardBody = 'cardBodyInclude/Content missing';
        }

        isset($this->params['cardFooterAdditionalClass']) ?
            $cardFooterAdditionalClass = $this->params['cardFooterAdditionalClass'] :
            $cardFooterAdditionalClass = '';

        if (isset($this->params['cardFooter']) && $this->params['cardFooter'] === true) {
            if (isset($this->params['cardFooterContent'])) {

                $cardFooter = $this->params['cardFooterContent'];

            } else if (isset($this->params['cardFooterInclude']) &&
                       isset($this->params['cardFooterIncludeParams'])
            ) {

                $cardFooter =
                    $this->view->getPartial(
                        $this->params['cardFooterInclude'],
                        $this->params['cardFooterIncludeParams']
                    );
            } else if (isset($this->params['cardFooterInclude'])) {

                $cardFooter =
                    $this->view->getPartial(
                        $this->params['cardFooterInclude'],
                        $this->params
                    );
            } else {
                $cardFooter = 'cardFooterInclude/Content missing';
            }
        } else {
            $cardFooter = false;
        }

        // card content
        $this->content .=
            '<div id="' .$hasCardId . '" class="card ' .
                $hasCardCollapsed . ' ' . $cardAdditionalClass . '"' . $cardIsWidget . '>';
        if ($this->params['cardType'] === 'widget') {

            if (isset($this->params['cardWidgetContent'])) {
                $this->content .= $this->params['cardWidgetContent'];
            } else {
                return '<span class="text-uppercase text-danger">ERROR: cardWidgetContent missing</span>';
            }

        } else {
            if (isset($this->params['cardHeader']) && $this->params['cardHeader'] !== false) {
                if (isset($this->params['cardHeaderHidden']) && $this->params['cardHeaderHidden'] === true) {
                    $cardHeaderHidden = 'hidden';
                }

                $this->content .=
                    '<div class="card-header rounded-0 ' . $cardType . ' ' . $cardHeaderAdditionalClass . '" ' . $headerIsWidget . ' ' . $cardHeaderHidden . '>
                        <h3 class="card-title">' . $cardIcon . '<span class="title ml-1">' . $cardTitle . '</span></h3>
                        <div class="card-tools">' . $cardSpan . $cardCollapsed . $tools . '</div>' .
                    '</div>';
            }

            $this->content .=
                '<div class="card-body ' . $cardBodyAdditionalClass . '"' . $bodyIsWidget . '>' . $cardBody . '</div>';

            if ($cardFooter) {
                $this->content .=
                    '<div class="card-footer ' . $cardFooterAdditionalClass . '">' . $cardFooter . '</div>';
            }
        }
        $this->content .= '</div>';
    }
}