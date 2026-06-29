<?php

namespace System\Base\Installer\Packages\Setup;

use System\Base\Installer\Packages\Setup\Schema\Basepackages\ActivityLogs;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\AddressBook;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\ApiClientServices\ApiClientServices;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\ApiClientServices\ApiClientServicesCalls;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\ApiClientServices\Apis\Repos;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\ContactBook;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Dashboards;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Dashboards\Widgets as DashboardsWidgets;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Email\Queue as EmailQueue;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Email\Services as EmailServices;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Filters;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Cities;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\CitiesIp2LocationV4;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\CitiesIp2LocationV6;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Countries;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Holidays;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Postcodes;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Regions;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\States;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Timezones;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\ImportExport;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Menus;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Messenger;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Murls;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Mutex;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Notes;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Notifications;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Pages;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Pages\Widgets as PagesWidgets;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages\StoragesLocal;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Tags;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Templates;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Agents;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\CanLogin;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Env;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Identifiers;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Security;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Sessions;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Tunnels;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Profiles;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Roles;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Widgets;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Calls;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Jobs;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Schedules;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Tasks;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Workers;
use System\Base\Installer\Packages\Setup\Schema\Modules\Bundles;
use System\Base\Installer\Packages\Setup\Schema\Modules\Components;
use System\Base\Installer\Packages\Setup\Schema\Modules\Externals;
use System\Base\Installer\Packages\Setup\Schema\Modules\Middlewares;
use System\Base\Installer\Packages\Setup\Schema\Modules\Packages;
use System\Base\Installer\Packages\Setup\Schema\Modules\Queues;
use System\Base\Installer\Packages\Setup\Schema\Modules\Views;
use System\Base\Installer\Packages\Setup\Schema\Modules\Views\Settings;
use System\Base\Installer\Packages\Setup\Schema\Providers\Access\IpFilters;
use System\Base\Installer\Packages\Setup\Schema\Providers\Access\IpFiltersDefault;
use System\Base\Installer\Packages\Setup\Schema\Providers\Access\IpFiltersIp2location;
use System\Base\Installer\Packages\Setup\Schema\Providers\Access\IpFiltersIp2locationCities;
use System\Base\Installer\Packages\Setup\Schema\Providers\Access\IpFiltersIp2locationCountries;
use System\Base\Installer\Packages\Setup\Schema\Providers\Access\IpFiltersIp2locationStates;
use System\Base\Installer\Packages\Setup\Schema\Providers\Api as SPApi;
use System\Base\Installer\Packages\Setup\Schema\Providers\Api\AccessTokens;
use System\Base\Installer\Packages\Setup\Schema\Providers\Api\AuthorizationCodes;
use System\Base\Installer\Packages\Setup\Schema\Providers\Api\Clients;
use System\Base\Installer\Packages\Setup\Schema\Providers\Api\RefreshTokens;
use System\Base\Installer\Packages\Setup\Schema\Providers\Api\Scopes;
use System\Base\Installer\Packages\Setup\Schema\Providers\Apps;
use System\Base\Installer\Packages\Setup\Schema\Providers\Apps\Types;
use System\Base\Installer\Packages\Setup\Schema\Providers\Cache;
use System\Base\Installer\Packages\Setup\Schema\Providers\Core;
use System\Base\Installer\Packages\Setup\Schema\Providers\Domains;
use System\Base\Installer\Packages\Setup\Schema\Providers\Logs;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFilters;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersDefault;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersIp2location;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersIp2locationCities;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersIp2locationCountries;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFiltersIp2locationStates;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApi;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiAccessTokens;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiAuthorizationCodes;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiRefreshTokens;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiScopes;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderApps;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderAppsTypes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices\BasepackagesApiClientServices;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices\BasepackagesApiClientServicesCalls;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesContactBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesDashboards;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesFilters;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesImportExport;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMenus;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMurls;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMutex;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotifications;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesPages;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesStorages;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesTags;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesTemplates;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesWidgets;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Dashboards\BasepackagesDashboardsWidgets;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email\BasepackagesEmailQueue;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email\BasepackagesEmailServices;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCities;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCountries;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoHolidays;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoPostcodes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoRegions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoStates;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoTimezones;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Messenger\BasepackagesMessenger;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Pages\BasepackagesPagesWidgets;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Storages\BasepackagesStoragesLocal;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsEnv;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSecurity;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsTunnels;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersProfiles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersRoles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersCalls;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersJobs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersSchedules;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersTasks;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersWorkers;
use System\Base\Providers\CoreServiceProvider\Model\ServiceProviderCore;
use System\Base\Providers\DomainsServiceProvider\Model\ServiceProviderDomains;
use System\Base\Providers\ModulesServiceProvider\Model\ServiceProviderModulesQueues;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesBundles;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesComponents;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesExternals;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesMiddlewares;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesPackages;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesViews;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesViewsSettings;

class Schema
{
    public function getSchema($dev)
    {
        $schema =
            [
                'service_provider_core'                     => [
                        'schema'    => new Core,
                        'model'     => new ServiceProviderCore,
                    ],
                'service_provider_apps'                     => [
                        'schema'    => new Apps,
                        'model'     => new ServiceProviderApps,
                    ],
                'service_provider_apps_types'               => [
                        'schema'    => new Types,
                        'model'     => new ServiceProviderAppsTypes,
                    ],
                'service_provider_access_ip_filters'        => [
                        'schema'    => new IpFilters,
                        'model'     => new ServiceProviderAccessIpFilters,
                    ],
                'service_provider_access_ip_filters_default'=> [
                        'schema'    => new IpFiltersDefault,
                        'model'     => new ServiceProviderAccessIpFiltersDefault,
                    ],
                'service_provider_access_ip_filters_ip2location'=> [
                        'schema'    => new IpFiltersIp2location,
                        'model'     => new ServiceProviderAccessIpFiltersIp2location,
                    ],
                'service_provider_access_ip_filters_ip2location_countries'=> [
                        'schema'    => new IpFiltersIp2locationCountries,
                        'model'     => new ServiceProviderAccessIpFiltersIp2locationCountries,
                    ],
                'service_provider_access_ip_filters_ip2location_states'=> [
                        'schema'    => new IpFiltersIp2locationStates,
                        'model'     => new ServiceProviderAccessIpFiltersIp2locationStates,
                    ],
                'service_provider_access_ip_filters_ip2location_cities'=> [
                        'schema'    => new IpFiltersIp2locationCities,
                        'model'     => new ServiceProviderAccessIpFiltersIp2locationCities,
                    ],
                'service_provider_domains'                  => [
                        'schema'    => new Domains,
                        'model'     => new ServiceProviderDomains,
                    ],
                'service_provider_modules_queues'           => [
                        'schema'    => new Queues,
                        'model'     => new ServiceProviderModulesQueues,
                    ],
                'modules_bundles'                           => [
                        'schema'    => new Bundles,
                        'model'     => new ModulesBundles,
                    ],
                'modules_components'                        => [
                        'schema'    => new Components,
                        'model'     => new ModulesComponents,
                    ],
                'modules_packages'                          => [
                        'schema'    => new Packages,
                        'model'     => new ModulesPackages,
                    ],
                'modules_middlewares'                       => [
                        'schema'    => new Middlewares,
                        'model'     => new ModulesMiddlewares,
                    ],
                'modules_views'                             => [
                        'schema'    => new Views,
                        'model'     => new ModulesViews,
                    ],
                'modules_views_settings'                    => [
                        'schema'    => new Settings,
                        'model'     => new ModulesViewsSettings,
                    ],
                'modules_externals'                         => [
                        'schema'    => new Externals,
                        'model'     => new ModulesExternals,
                    ],
                'basepackages_tags'                         => [
                        'schema'    => new Tags,
                        'model'     => new BasepackagesTags,
                    ],
                'basepackages_pages'                        => [
                        'schema'    => new Pages,
                        'model'     => new BasepackagesPages,
                    ],
                'basepackages_pages_widgets'                => [
                        'schema'    => new PagesWidgets,
                        'model'     => new BasepackagesPagesWidgets,
                    ],
                'basepackages_email_services'               => [
                        'schema'    => new EmailServices,
                        'model'     => new BasepackagesEmailServices,
                    ],
                'basepackages_email_queue'                  => [
                        'schema'    => new EmailQueue,
                        'model'     => new BasepackagesEmailQueue,
                    ],
                'basepackages_users_accounts'               => [
                        'schema'    => new Accounts,
                        'model'     => new BasepackagesUsersAccounts,
                    ],
                'basepackages_users_accounts_security'      => [
                        'schema'    => new Security,
                        'model'     => new BasepackagesUsersAccountsSecurity,
                    ],
                'basepackages_users_accounts_canlogin'      => [
                        'schema'    => new CanLogin,
                        'model'     => new BasepackagesUsersAccountsCanlogin,
                    ],
                'basepackages_users_accounts_sessions'      => [
                        'schema'    => new Sessions,
                        'model'     => new BasepackagesUsersAccountsSessions,
                    ],
                'basepackages_users_accounts_identifiers'   => [
                        'schema'    => new Identifiers,
                        'model'     => new BasepackagesUsersAccountsIdentifiers,
                    ],
                'basepackages_users_accounts_agents'        => [
                        'schema'    => new Agents,
                        'model'     => new BasepackagesUsersAccountsAgents,
                    ],
                'basepackages_users_accounts_tunnels'       => [
                        'schema'    => new Tunnels,
                        'model'     => new BasepackagesUsersAccountsTunnels,
                    ],
                'basepackages_users_accounts_env'           => [
                        'schema'    => new Env,
                        'model'     => new BasepackagesUsersAccountsEnv,
                    ],
                'basepackages_users_profiles'               => [
                        'schema'    => new Profiles,
                        'model'     => new BasepackagesUsersProfiles,
                    ],
                'basepackages_users_roles'                  => [
                        'schema'    => new Roles,
                        'model'     => new BasepackagesUsersRoles,
                    ],
                'basepackages_menus'                        => [
                        'schema'    => new Menus,
                        'model'     => new BasepackagesMenus,
                    ],
                'basepackages_murls'                        => [
                        'schema'    => new Murls,
                        'model'     => new BasepackagesMurls,
                    ],
                'basepackages_mutex'                        => [
                        'schema'    => new Mutex,
                        'model'     => new BasepackagesMutex,
                    ],
                'basepackages_filters'                      => [
                        'schema'    => new Filters,
                        'model'     => new BasepackagesFilters,
                    ],
                'basepackages_geo_regions'                  => [
                        'schema'    => new Regions,
                        'model'     => new BasepackagesGeoRegions,
                    ],
                'basepackages_geo_holidays'                 => [
                        'schema'    => new Holidays,
                        'model'     => new BasepackagesGeoHolidays,
                    ],
                'basepackages_geo_countries'                => [
                        'schema'    => new Countries,
                        'model'     => new BasepackagesGeoCountries,
                    ],
                'basepackages_geo_states'                   => [
                        'schema'    => new States,
                        'model'     => new BasepackagesGeoStates,
                    ],
                'basepackages_geo_cities'                   => [
                        'schema'    => new Cities,
                        'model'     => new BasepackagesGeoCities,
                    ],
                'basepackages_geo_postcodes'                => [
                        'schema'    => new Postcodes,
                        'model'     => new BasepackagesGeoPostcodes,
                    ],
                'basepackages_geo_timezones'                => [
                        'schema'    => new Timezones,
                        'model'     => new BasepackagesGeoTimezones,
                    ],
                'basepackages_address_book'                 => [
                        'schema'    => new AddressBook,
                        'model'     => new BasepackagesAddressBook,
                    ],
                'basepackages_contact_book'                 => [
                        'schema'    => new ContactBook,
                        'model'     => new BasepackagesContactBook,
                    ],
                'basepackages_storages'                     => [
                        'schema'    => new Storages,
                        'model'     => new BasepackagesStorages,
                    ],
                'basepackages_storages_local'               => [
                        'schema'    => new StoragesLocal,
                        'model'     => new BasepackagesStoragesLocal,
                    ],
                'basepackages_activity_logs'                => [
                        'schema'    => new ActivityLogs,
                        'model'     => new BasepackagesActivityLogs,
                    ],
                'basepackages_notes'                        => [
                        'schema'    => new Notes,
                        'model'     => new BasepackagesNotes,
                    ],
                'basepackages_notifications'                => [
                        'schema'    => new Notifications,
                        'model'     => new BasepackagesNotifications,
                    ],
                'basepackages_workers_workers'              => [
                        'schema'    => new Workers,
                        'model'     => new BasepackagesWorkersWorkers,
                    ],
                'basepackages_workers_schedules'            => [
                        'schema'    => new Schedules,
                        'model'     => new BasepackagesWorkersSchedules,
                    ],
                'basepackages_workers_calls'                => [
                        'schema'    => new Calls,
                        'model'     => new BasepackagesWorkersCalls,
                    ],
                'basepackages_workers_tasks'                => [
                        'schema'    => new Tasks,
                        'model'     => new BasepackagesWorkersTasks,
                    ],
                'basepackages_workers_jobs'                 => [
                        'schema'    => new Jobs,
                        'model'     => new BasepackagesWorkersJobs,
                    ],
                'basepackages_import_export'                => [
                        'schema'    => new ImportExport,
                        'model'     => new BasepackagesImportExport,
                    ],
                'basepackages_templates'                    => [
                        'schema'    => new Templates,
                        'model'     => new BasepackagesTemplates,
                    ],
                'basepackages_dashboards'                   => [
                        'schema'    => new Dashboards,
                        'model'     => new BasepackagesDashboards,
                    ],
                'basepackages_dashboards_widgets'           => [
                        'schema'    => new DashboardsWidgets,
                        'model'     => new BasepackagesDashboardsWidgets,
                    ],
                'basepackages_widgets'                      => [
                        'schema'    => new Widgets,
                        'model'     => new BasepackagesWidgets,
                    ],
                'basepackages_messenger'                    => [
                        'schema'    => new Messenger,
                        'model'     => new BasepackagesMessenger,
                    ],
                'basepackages_api_client_services'          => [
                        'schema'    => new ApiClientServices,
                        'model'     => new BasepackagesApiClientServices,
                    ],
                'basepackages_api_client_services_calls'    => [
                        'schema'    => new ApiClientServicesCalls,
                        'model'     => new BasepackagesApiClientServicesCalls,
                    ],
                'basepackages_api_client_services_apis_repos'=> [
                        'schema'    => new Repos,
                        'model'     => null
                    ],
                'service_provider_api'                       => [
                        'schema'    => new SPApi,
                        'model'     => new ServiceProviderApi,
                    ],
                'service_provider_api_access_tokens'         => [
                        'schema'    => new AccessTokens,
                        'model'     => new ServiceProviderApiAccessTokens,
                    ],
                'service_provider_api_authorization_codes'   => [
                        'schema'    => new AuthorizationCodes,
                        'model'     => new ServiceProviderApiAuthorizationCodes,
                    ],
                'service_provider_api_clients'               => [
                        'schema'    => new Clients,
                        'model'     => new ServiceProviderApiClients,
                    ],
                'service_provider_api_refresh_tokens'        => [
                        'schema'    => new RefreshTokens,
                        'model'     => new ServiceProviderApiRefreshTokens,
                    ],
                'service_provider_api_scopes'                => [
                        'schema'    => new Scopes,
                        'model'     => new ServiceProviderApiScopes,
                    ],
                'service_provider_api_scopes'                => [
                        'schema'    => new Scopes,
                        'model'     => new ServiceProviderApiScopes,
                    ]
            ];

        if ($dev == 'true') {
            $schema['apps_core_devtools_files_hash'] = [
                    'schema'    => new \Apps\Core\Packages\Devtools\Modules\Install\Schema\FilesHash,
                    'model'     => new \Apps\Core\Packages\Devtools\Modules\Model\AppsCoreDevtoolsFilesHash,
                ];
            $schema['devtools_test'] = [
                    'schema'    => new \Apps\Core\Packages\Devtools\Test\Install\Schema\DevtoolsTest,
                    'model'     => new \Apps\Core\Packages\Devtools\Test\Model\DevtoolsTest,
                ];
        }

        return $schema;
    }
}