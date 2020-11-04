<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class Card extends AdminLTETags
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
        if (isset($this->params['componentId']) && isset($this->params['sectionId'])) {
            $hasCardId = $this->params['componentId'] . '-' . $this->params['sectionId'] . '-card';
        } else if (!isset($this->params['componentId']) && isset($this->params['sectionId'])) {
            $hasCardId = $this->params['sectionId'] . '-card';
        } else if (isset($this->params['cardId'])) {
            $hasCardId = $this->params['cardId'] . '-card';
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

        if (isset($this->params['cardShowTools']) && count($this->params['cardShowTools']) > 0) {
            $tools = '';

            foreach ($this->params['cardShowTools'] as $key => $tool) {
                if ($tool === "navigators") {
                    $tools .= '<div class="section-navigators"></div>';
                } else if ($tool === "jstreeTools") {
                    $tools .=
                        '<button type="button" id="collapseTreeCards" class="btn btn-tool">
                            <i class="fas fa-fw fa-compress-arrows-alt"></i>
                        </button>
                        <button type="button" id="expandTreeCards" class="btn btn-tool">
                            <i class="fas fa-fw fa-expand-arrows-alt"></i>
                        </button>';
                } else if ($tool === "refresh") {
                    $tools .=
                        '<button type="button" class="btn btn-tool" data-card-widget="refresh"' . $cardRefreshSource . ' ' . $cardRefreshParams . ' ' . $cardRefreshDataType . ' ' . $cardRefreshMethod . ' ' . $cardRefreshSourceSelector . '>
                            <i class="fas fa-fw fa-sync-alt"></i>
                        </button>';
                } else if ($tool === "maximize") {
                    $tools .=
                        '<button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-fw fa-expand"></i>
                        </button>';
                } else if (!isset($this->params['cardCollapsed']) ||
                           ($this->params['cardCollapsed'] === false && $tool === "collapse")
                ) {
                    $tools .=
                        '<button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-fw fa-minus"></i>
                        </button>';
                } else if ($tool === "remove") {
                    $tools .=
                        '<button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-fw fa-times"></i>
                        </button>';
                }
            }
        } else {
            $tools = '';
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

        if (isset($this->params['cardFooterContent'])) {
            $cardFooter = $this->params['cardFooterContent'];
        } else {
            $cardFooter = '';
        }

        // card content
        $this->content .=
            '<div id="' .$hasCardId . '" class="card ' .
                $hasCardCollapsed . ' ' . $cardAdditionalClass . '">';
        if ($this->params['cardType'] === 'widget') {

            if (isset($this->params['cardWidgetContent'])) {
                $this->content .= $this->params['cardWidgetContent'];
            } else {
                return '<span class="text-uppercase text-danger">ERROR: cardWidgetContent missing</span>';
            }

        } else {
            if (isset($this->params['cardHeader'])) {
                $this->content .=
                    '<div class="card-header rounded-0 ' . $cardType . ' ' . $cardHeaderAdditionalClass . '">
                        <h3 class="card-title">' . $cardIcon . '<span class="title ml-1">' . $cardTitle . '</span></h3>
                        <div class="card-tools">' . $cardSpan . $cardCollapsed . $tools . '</div>' .
                    '</div>';
            }

            $this->content .=
                '<div class="card-body ' . $cardBodyAdditionalClass . '">' . $cardBody . '</div>';

            if (isset($this->params['cardFooter'])) {
                $this->content .=
                    '<div class="card-footer ' . $cardFooterAdditionalClass . '">' . $cardFooter . '</div>';

            }
        }
        $this->content .= '</div>';
    }
}