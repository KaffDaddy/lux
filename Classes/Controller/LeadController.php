<?php

declare(strict_types=1);
namespace In2code\Lux\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception as ExceptionDbalDriver;
use Doctrine\DBAL\Exception as ExceptionDbal;
use In2code\Lux\Domain\DataProvider\IdentificationMethodsDataProvider;
use In2code\Lux\Domain\DataProvider\PagevisistsDataProvider;
use In2code\Lux\Domain\DataProvider\ReferrerAmountDataProvider;
use In2code\Lux\Domain\Model\Transfer\FilterDto;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Exception\ConfigurationException;
use In2code\Lux\Exception\UnexpectedValueException;
use In2code\Lux\Utility\LocalizationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Fluid\View\StandaloneView;

class LeadController extends AbstractController
{
    /**
     * @return void
     * @throws NoSuchArgumentException
     */
    public function initializeDashboardAction(): void
    {
        $this->setFilter(FilterDto::PERIOD_LAST3MONTH);
    }

    /**
     * @param FilterDto $filter
     * @return ResponseInterface
     * @throws ConfigurationException
     * @throws ExceptionDbal
     * @throws ExceptionDbalDriver
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidConfigurationTypeException
     * @throws InvalidQueryException
     * @throws UnexpectedValueException
     * @throws DBALException
     */
    public function dashboardAction(FilterDto $filter): ResponseInterface
    {
        $this->cacheLayer->initialize(__CLASS__, __FUNCTION__);
        $this->view->assignMultiple([
            'cacheLayer' => $this->cacheLayer,
            'filter' => $filter,
            'interestingLogs' => $this->logRepository->findInterestingLogs($filter, 10),
            'whoisonline' => $this->visitorRepository->findOnline(8),
        ]);

        if ($this->cacheLayer->isCacheAvailable('Box/Leads/Recurring/' . $filter->getHash()) === false) {
            $this->view->assignMultiple([
                'numberOfUniqueSiteVisitors' => $this->visitorRepository->findByUniqueSiteVisits($filter)->count(),
                'numberOfRecurringSiteVisitors' =>
                    $this->visitorRepository->findByRecurringSiteVisits($filter)->count(),
                'identifiedPerMonth' => $this->logRepository->findIdentifiedLogsFromMonths(6),
                'numberOfIdentifiedVisitors' => $this->visitorRepository->findIdentified($filter)->count(),
                'numberOfUnknownVisitors' => $this->visitorRepository->findUnknown($filter)->count(),
                'identificationMethods' =>
                    GeneralUtility::makeInstance(IdentificationMethodsDataProvider::class, $filter),
                'referrerAmountData' => GeneralUtility::makeInstance(ReferrerAmountDataProvider::class, $filter),
                'countries' => $this->ipinformationRepository->findAllCountryCodesGrouped($filter),
                'hottestVisitors' => $this->visitorRepository->findByHottestScorings($filter),
                'renderingTime' => $this->renderingTimeService->getTime(),
            ]);
        }

        $this->addDocumentHeaderForCurrentController();
        return $this->defaultRendering();
    }

    /**
     * @return void
     * @throws NoSuchArgumentException
     */
    public function initializeListAction(): void
    {
        $this->setFilter();
    }

    /**
     * @param FilterDto $filter
     * @param string $export
     * @return ResponseInterface
     * @throws ExceptionDbal
     * @throws ExceptionDbalDriver
     * @throws InvalidQueryException
     */
    public function listAction(FilterDto $filter, string $export = ''): ResponseInterface
    {
        if ($export === 'csv') {
            return (new ForwardResponse('downloadCsv'))->withArguments(['filter' => $filter]);
        }
        $this->view->assignMultiple([
            'numberOfVisitorsData' => GeneralUtility::makeInstance(PagevisistsDataProvider::class, $filter),
            'hottestVisitors' => $this->visitorRepository->findByHottestScorings($filter, 8),
            'filter' => $filter,
            'allVisitors' => $this->visitorRepository->findAllWithIdentifiedFirst($filter),
            'luxCategories' => $this->categoryRepository->findAllLuxCategories(),
        ]);

        $this->addDocumentHeaderForCurrentController();
        return $this->defaultRendering();
    }

    /**
     * @param FilterDto $filter
     * @return ResponseInterface
     * @throws InvalidQueryException
     */
    public function downloadCsvAction(FilterDto $filter): ResponseInterface
    {
        $this->view->assignMultiple([
            'allVisitors' => $this->visitorRepository->findAllWithIdentifiedFirst($filter),
        ]);
        return $this->csvResponse($this->view->render());
    }

    /**
     * @param Visitor $visitor
     * @return ResponseInterface
     */
    public function detailAction(Visitor $visitor): ResponseInterface
    {
        $this->view->assign('visitor', $visitor);

        $this->addDocumentHeaderForCurrentController();
        return $this->defaultRendering();
    }

    /**
     * Really remove visitor completely from db (not only deleted=1)
     *
     * @param Visitor $visitor
     * @return ResponseInterface
     * @throws DBALException
     */
    public function removeAction(Visitor $visitor): ResponseInterface
    {
        $this->visitorRepository->removeVisitor($visitor);
        $this->visitorRepository->removeRelatedTableRowsByVisitor($visitor);
        $this->addFlashMessage('Visitor completely removed from database');
        return $this->redirect('list');
    }

    /**
     * @param Visitor $visitor
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws DBALException
     */
    public function deactivateAction(Visitor $visitor): ResponseInterface
    {
        $visitor->setBlacklistedStatus();
        $this->visitorRepository->update($visitor);
        $this->addFlashMessage('Visitor is blacklisted now');
        return $this->redirect('list');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @noinspection PhpUnused
     */
    public function detailAjax(ServerRequestInterface $request): ResponseInterface
    {
        $response = GeneralUtility::makeInstance(JsonResponse::class);
        $visitorRepository = GeneralUtility::makeInstance(VisitorRepository::class);
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:lux/Resources/Private/Templates/Lead/ListDetailAjax.html'
        ));
        $standaloneView->setPartialRootPaths(['EXT:lux/Resources/Private/Partials/']);
        $standaloneView->assignMultiple([
            'visitor' => $visitorRepository->findByUid((int)$request->getQueryParams()['visitor']),
        ]);
        $response->getBody()->write(json_encode(['html' => $standaloneView->render()]));
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @noinspection PhpUnused
     */
    public function detailDescriptionAjax(ServerRequestInterface $request): ResponseInterface
    {
        $response = GeneralUtility::makeInstance(JsonResponse::class);
        $visitorRepository = GeneralUtility::makeInstance(VisitorRepository::class);
        /** @var Visitor $visitor */
        $visitor = $visitorRepository->findByUid((int)$request->getQueryParams()['visitor']);
        $visitor->setDescription($request->getQueryParams()['value']);
        $visitorRepository->update($visitor);
        $visitorRepository->persistAll();
        return $response;
    }

    /**
     * @return void
     */
    protected function addDocumentHeaderForCurrentController(): void
    {
        $actions = ['dashboard', 'list'];
        $menuConfiguration = [];
        foreach ($actions as $action) {
            $menuConfiguration[$action] = LocalizationUtility::translate(
                'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:module.lead.' . $action
            );
        }
        $this->addDocumentHeader($menuConfiguration);
    }
}
