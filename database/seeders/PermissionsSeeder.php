<?php

namespace Database\Seeders;

use App\Enums\PermissionsLevel;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $homeDashboardPermissions = [
            'app.dashboard.view',
            'app.dashboard.approvals',
            'app.dashboard.quick_access',
            'app.dashboard.analytics',
        ];

        $approvalsPermissions = [
            'app.approval.manage',
            'treaty.approval.initial_review',
            'treaty.approval.technical_assessment',
            'treaty.approval.financial_review',
            'treaty.approval.risk_evaluation',
            'treaty.approval.final_authorization',
            'treaty.approval.modify_terms',
            'treaty.approval.reject',
            'treaty.approval.escalate',
            //
            'facultative.approval.submission_review',
            'facultative.approval.risk_assessment',
            'facultative.approval.pricing_review',
            'facultative.approval.final_decision',
            'facultative.approval.final_authorization',
            'facultative.approval.modify_terms',
            'facultative.approval.reject',
            'facultative.approval.escalate',
            //
            'claims.approval.initial_notification',
            'claims.approval.preliminary_assessment',
            'claims.approval.technical_review',
            'claims.approval.financial_validation',
            'claims.approval.final_authorization',
            'claims.approval.modify_terms',
            'claims.approval.reject_claim',
            'claims.approval.escalate',
            //
            'advanced.approval.cross_department_approvals',
            'advanced.approval.approval_override',
            'advanced.approval.audit_approval_changes',
            'advanced.approval.emergency_approvals',
        ];

        $businessDevelopmentPermissions = [
            'app.business_development.lead_management',
            'app.business_development.opportunity_tracking',
            'app.business_development.prospect_research',
            'app.business_development.strategy_development',
            'app.business_development.view',
            //
            'client.prospecting.create',
            'client.prospecting.view',
            'client.prospecting.edit',
            'client.prospecting.delete',
            'client.prospecting.log',
            'client.meeting.schedule',
            'client.proposal.generate',
            'client.proposal.send',
            //
            'pipeline.lead.create',
            'pipeline.lead.qualify',
            'pipeline.opportunity.score',
            'pipeline.conversion.track',
            'pipeline.forecast.analyze',
            'pipeline.stage.generate',
            'pipeline.progress.monitor',
            //
            'onboarding.process.design',
            'onboarding.client.assess',
            'onboarding.documentation.manage',
            'onboarding.compliance.check',
            'onboarding.integration.plan',
            'onboarding.training.coordinate',
            'onboarding.feedback.collect',
            'onboarding.success.track',
            //
            'business_development.strategy.develop',
            'business_development.strategy.review',
            'business_development.strategy.update',
            'business_development.strategy.share',
            'business_development.strategy.performance.measure',
            'business_development.strategy.market.align',
            'business_development.strategy.risk.assess',
            'business_development.strategy.innovation.explore',
            //
            'business_development.proposal.create',
            'business_development.proposal.customize',
            'business_development.proposal.price',
            'business_development.proposal.review',
            'business_development.proposal.approve',
            'business_development.proposal.track',
            'business_development.proposal.archive',
            'business_development.proposal.version_control',
            //
            'business_development.pipeline.view',
            'business_development.pipeline.create',
            'business_development.pipeline.edit',
            'business_development.pipeline.delete',
            'business_development.pipeline.move_stages',
            'business_development.pipeline.bulk_update',
            'business_development.pipeline.export',
            'business_development.pipeline.import',
            //
            'business_development.facultative.view',
            //
            'business_development.treaty.view',
            //
            'business_development.sales_management.create_custom',
            'business_development.sales_management.modify',
            'business_development.sales_management.delete',
            'business_development.sales_management.view',
            'business_development.sales_management.track_progression',
            'business_development.sales_management.set_criteria',
            'business_development.sales_management.automated_movement',
            //
            'business_development.performance.individual_track',
            'business_development.performance.team_track',
            'business_development.performance.metrics_view',
            'business_development.performance.goals_set',
            'business_development.performance.commission_calculate',
            'business_development.performance.ranking_view',
            'business_development.performance.benchmarking',
            'business_development.performance.progress_report',
            //
            'business_development.bd_handovers.view',
            'business_development.bd_handovers.create_draft',
            'business_development.bd_handovers.start_process',
            'business_development.bd_handovers.define_scope',
            'business_development.bd_handovers.set_timeline',
            'business_development.bd_handovers.assign_responsible_parties',
            'business_development.bd_handovers.preliminary_documentation',
            'business_development.bd_handovers.kick_off_meeting',
        ];

        $coverAdministrationPermissions = [
            'app.cover_administration.view',
            'app.cover_administration.manage',
            'cover.type.edit',
            'cover.type.delete',
            'cover.type.classify',
            'cover.type.archive',
            'cover.type.restore',
            //
            'cover.cedants.view',
            //
            'cover.treaty.view',
            'cover.treaty_non_proportional.view',
            //
            'cover.facultative.view',
            'cover.facultative_non_proportional.view',
            //
            'cover.details.create',
            'cover.details.edit',
            'cover.details.update',
            'cover.details.verify',
            'cover.details.historical_track',
            'cover.details.version_control',
            'cover.details.comprehensive_review',
            //
            'cover.risk.initial_assessment',
            'cover.risk.detailed_evaluation',
            'cover.risk.classification',
            'cover.risk.scoring',
            'cover.risk.mitigation_strategy',
            'cover.risk.periodic_review',
            'cover.risk.historical_analysis',
            'cover.risk.predictive_modeling',
            //
            'cover.endorsement.create',
            'cover.endorsement.view',
            'cover.endorsement.process',
            'cover.endorsement.approve',
            'cover.endorsement.modify',
            'cover.endorsement.cancel',
            'cover.endorsement.historical_track',
            'cover.endorsement.impact_assess',
            //
            'cover.geography.define',
            'cover.geography.expand',
            'cover.line_of_business.categorize',
            'cover.line_of_business.risk_assess',
            'cover.geography.restrict',
            'cover.geography.compliance_check',
            //
            'cedant.profile.view',
            'cedant.profile.create',
            'cedant.profile.update',
            'cedant.profile.verify',
            'cedant.profile.risk_rating',
            'cedant.profile.financial_health',
            'cedant.profile.business_segment',
            'cedant.profile.historical_performance',
            //
            'cedant.treaty.create',
            'cedant.treaty.view',
            'cedant.treaty.negotiate',
            'cedant.treaty.modify',
            'cedant.treaty.approve',
            'cedant.treaty.renewal_track',
            'cedant.treaty.termination_process',
            'cedant.treaty.historical_review',
            //
            'cedant.portfolio.view',
            'cedant.portfolio.analysis',
            'cedant.portfolio.composition',
            'cedant.portfolio.risk_balance',
            'cedant.portfolio.performance_track',
            'cedant.portfolio.optimization',
            'cedant.portfolio.segment_review',
            'cedant.portfolio.future_strategy',
        ];

        $claimsAdministrationPermissions = [
            'app.claims_administration.view',
            'app.claims_administration.manage',
            'claims.notification.verify',
            //
            'claims.notification.view',
            //
            'claims.enquiry.view',
            //
            'app.claims.edit',
            'app.claims.delete',
            'app.claims.approve',
            'app.claims.reject',
            'app.claims.reassign',
            'app.claims.escalate',
            'app.claims.historical_review',
            'app.claims.export'
        ];

        $analyticsPermissions = [
            'app.analytics.data.collect',
            'app.analytics.data.analyze',
            'app.analytics.report.generate',
            'app.analytics.insight.derive',
            'app.analytics.dashboard.access',
            'app.analytics.trend.predict',
            'app.analytics.competitive.monitor',
            'app.analytics.decision.support',
            'app.analytics.performance.measure',
            'app.analytics.conversion.track',
            'app.analytics.revenue.forecast',
            'app.analytics.win_rate.analyze',
            'app.analytics.lead_source.evaluate',
            'app.analytics.sales_cycle.optimize',
            'app.analytics.team_performance.assess',
            'app.analytics.territory.map',
            //
            'app.analytics.pipeline_overview',
            'app.analytics.conversion_rate',
            'app.analytics.win_loss_analysis',
            'app.analytics.sales_cycle_length',
            'app.analytics.revenue_projection',
            'app.analytics.lead_source_effectiveness',
            'app.analytics.territory_performance',
            //
            'treaty.analytics.performance_track',
            'treaty.analytics.loss_ratio_analyze',
            'treaty.analytics.claim_frequency',
            'treaty.analytics.portfolio_optimization',
            'treaty.analytics.trend_identification',
            'treaty.analytics.comparative_analysis',
            'treaty.reporting.generate',
            'treaty.reporting.custom_dashboard',
            //
            'app.analytics.comparative_performance',
            'app.analytics.type_efficiency',
            'app.analytics.risk_distribution',
            'app.analytics.profitability_comparison',
            'app.analytics.market_positioning',
            'app.analytics.predictive_insights',
            'app.analytics.trend_identification',
            'app.analytics.strategic_recommendation',

        ];

        $businessIntelligence = [
            'intelligence.data.collect',
            'intelligence.data.analyze',
            'intelligence.report.generate',
            'intelligence.insight.derive',
            'intelligence.dashboard.access',
            'intelligence.trend.predict',
            'intelligence.competitive.monitor',
            'intelligence.decision.support',
            //
            'cedant.intelligence.portfolio_analysis',
            'cedant.intelligence.risk_concentration',
            'cedant.intelligence.loss_ratio_track',
            'cedant.intelligence.claims_history',
            'cedant.intelligence.market_positioning',
            'cedant.intelligence.competitive_benchmark',
            'cedant.intelligence.trend_analysis',
            'cedant.intelligence.predictive_modeling',
            //
            'cedant.performance.overall_assessment',
            'cedant.performance.loss_ratio',
            'cedant.performance.profitability',
            'cedant.performance.claims_frequency',
            'cedant.performance.comparative_analysis',
            'cedant.performance.trend_identification',
            'cedant.performance.predictive_insights',
            'cedant.performance.benchmark_comparison',
            //

        ];

        $treatyManagementPermissions = [
            'app.treaty.renew',
            'app.treaty.set_terms',
            'app.treaty.set_limits',
            'app.treaty.create',
            'app.treaty.view',
            'app.treaty.edit',
            'app.treaty.delete',
            'app.treaty.archive',
            'app.treaty.restore',
            'app.treaty.version_control',
            'app.treaty.comprehensive_review',
            //
            'app.treaty.quota_share.create',
            'app.treaty.quota_share.view',
            'app.treaty.quota_share.edit',
            'app.treaty.quota_share.percentage_adjust',
            'app.treaty.quota_share.capacity_manage',
            'app.treaty.quota_share.premium_calculation',
            'app.treaty.quota_share.commission_structure',
            'app.treaty.quota_share.performance_track',
        ];

        $facultativePermissions = [
            'facultative.submission.create',
            'facultative.submission.view',
            'facultative.submission.edit',
            'facultative.submission.delete',
            'facultative.submission.track',
            'facultative.submission.prioritize',
            'facultative.submission.bulk_manage',
            'facultative.submission.historical_review',
            //
            'non_facultative.treaty.create',
            'non_facultative.treaty.view',
            'non_facultative.treaty.edit',
            'non_facultative.treaty.terminate',
            'non_facultative.treaty.renewal_track',
            'non_facultative.treaty.performance_review',
            'non_facultative.treaty.capacity_manage',
            'non_facultative.treaty.historical_analysis'
        ];

        $rolePermissions = [
            'app.role.create',
            'app.role.view',
            'app.role.edit',
            'app.role.delete',
            'app.role.assign_permissions'
        ];

        $Permissions = [
            'app.permission.create',
            'app.permission.view',
            'app.permission.edit',
            'app.permission.delete'
        ];

        $authPermissions = [
            'app.auth.login',
            'app.auth.logout',
            'app.auth.register',
            'app.auth.password_reset',
            'app.auth.two_factor_setup'
        ];


        $systemSettingsPermissions = [
            'app.system_settings.view',
            //
            'app.reins_settings.view',
            //
            'app.user_management.view',
            //
            'app.integrations_api.view',
            //
            'system_settings.profile.view',
            'system_settings.departments.view',
            'system_settings.branches.view',
            'system_settings.system_processes.view',
            'system_settings.system_actions.view',
            'system_settings.company_profile.view',
            'system_settings.general_config.view',
            //
            'reins_settings.customers.view',
            'reins_settings.class_groups.view',
            'reins_settings.classes.view',
            'reins_settings.policy_clauses.view',
            'reins_settings.sum_insured_types.view',
            'reins_settings.rein_division.view',
            'reins_settings.rein_classes.view',
            'reins_settings.rein_class_prem_types.view',
            'reins_settings.treaty_types.view',
            'reins_settings.customer_types.view',
            'reins_settings.countries.view',
            'reins_settings.business_types.view',
            'reins_settings.binder_classes.view',
            'reins_settings.pay_methods.view',
            'app.system.maintenance',
            'app.system.logs_view',
            'app.system.backup',
            'app.system.restore',
            'app.system.integration_manage',
        ];

        $communicationPermissions = [
            'app.communication.send_email',
            'app.communication.send_notification',
            'app.communication.manage_templates',
            'app.communication.view_logs'
        ];

        $apiPermissions = [
            'app.api.generate_token',
            'app.api.view_tokens',
            'app.api.revoke_tokens',
            'app.integration.create',
            'app.integration.view',
            'app.integration.manage'
        ];

        $notificationPermission = [
            'app.notification.manage_channels',
            'app.notification.customize_preferences',
            'app.notification.view_history'
        ];

        $reportingPermissions = [
            'app.reports.view',
            'reports.budget_allocation.view',
            'business_development.budget_tracker.view',
            'business_development.dashboard.view',
            'business_development.cover.view',
            'business_development.claims.view',
            'business_development.debtors.view',
            'reports.pipeline.view',
            'reports.sales.view',
            'reports.facultative.view',
            //
            'cedant.reporting.generate',
            'cedant.reporting.view',
            'cedant.reporting.customize',
            'cedant.reporting.share',
            'cedant.reporting.periodic_review',
            'cedant.reporting.regulatory_compliance',
            'cedant.reporting.historical_track',
            'cedant.reporting.export',
            'report.generate',
            'report.view',
            'report.export',
            'report.schedule',
            'report.custom_design'
        ];

        $userPermissions = [
            'user_management.users.view',
            'user_management.roles.view',
            'user_management.permissions.view',
            'user.create',
            'user.view',
            'user.edit',
            'user.delete',
            'user.restore',
            'user.force_delete',
            'user.assign_role',
            'user.change_password',
            'user.impersonate',
            'user.profile_update'
        ];

        $helpPermissions = [
            'app.help.view'
        ];

        // Combine all permission groups
        $allPermissions = array_merge(
            $homeDashboardPermissions,
            $approvalsPermissions,
            $businessDevelopmentPermissions,
            $coverAdministrationPermissions,
            $analyticsPermissions,
            $businessIntelligence,
            $treatyManagementPermissions,
            $facultativePermissions,
            $rolePermissions,
            $Permissions,
            $authPermissions,
            $claimsAdministrationPermissions,
            $systemSettingsPermissions,
            $communicationPermissions,
            $apiPermissions,
            $notificationPermission,
            $userPermissions,
            $reportingPermissions,
            $helpPermissions
        );

        $defaultActivePermissions = [
            'app.dashboard.view',
            'app.dashboard.approvals',
            'app.business_development.view',
            'app.cover_administration.view',
            'app.claims_administration.view',
            'business_development.pipeline.view',
            'business_development.facultative.view',
            'business_development.treaty.view',
            'business_development.sales_management.view',
            'business_development.bd_handovers.view',
            'cover.cedants.view',
            'cover.treaty.view',
            'cover.treaty_non_proportional.view',
            'cover.facultative.view',
            'claims.notification.view',
            'claims.enquiry.view',
            'app.system_settings.view',
            'app.reins_settings.view',
            'app.user_management.view',
            'app.integrations_api.view',
            'system_settings.profile.view',
            'system_settings.departments.view',
            'system_settings.system_processes.view',
            'system_settings.branches.view',
            'system_settings.system_actions.view',
            'system_settings.company_profile.view',
            'system_settings.general_config.view',
            'reins_settings.customers.view',
            'reins_settings.class_groups.view',
            'reins_settings.classes.view',
            'reins_settings.policy_clauses.view',
            'reins_settings.sum_insured_types.view',
            'reins_settings.rein_division.view',
            'reins_settings.rein_classes.view',
            'reins_settings.rein_class_prem_types.view',
            'reins_settings.treaty_types.view',
            'reins_settings.customer_types.view',
            'reins_settings.countries.view',
            'reins_settings.business_types.view',
            'reins_settings.binder_classes.view',
            'reins_settings.pay_methods.view',
            'user_management.users.view',
            'user_management.roles.view',
            'user_management.permissions.view',
            'app.reports.view',
            'app.help.view',
            'app.dashboard.analytics',
            'business_development.budget_tracker.view',
            'business_development.dashboard.view',
            'business_development.cover.view',
            'business_development.claims.view',
            'business_development.debtors.view',
            'reports.pipeline.view',
            'reports.sales.view',
            'reports.facultative.view',
            'reports.budget_allocation.view'
        ];

        // clean permissions and relationships
        self::deepCleanPermissions();

        foreach ($allPermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'status' => in_array($permission, $defaultActivePermissions) ? 'A' : 'P',
                'permission_code' => Str::slug($permission) . '-' . rand(2030, 9999),
                'description' => ucwords(str_replace(['_', '.'], ' ', $permission)) . ' Permission'
            ]);
        }

        $roles = [
            'super_admin' => $allPermissions,
            'admin' => array_merge(
                $homeDashboardPermissions,
                $facultativePermissions
            ),
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => Str::title(str_replace('_', ' ', $roleName)),
                'slug' => $roleName,
                'department_code' => 2030,
                'description' => Str::kebab($roleName) . ' created on: ' . now()->format('Y-m-d H:i:s'),
                'permission_level' => PermissionsLevel::SUPERADMIN(),
            ]);
            $role->syncPermissions($permissions);
        }

        $adminRole = Role::where('slug', 'super_admin')->first();

        $userAssignments = [
            'pknuek'       => ['super_admin', 'Admin', '--', '+254700000000'],
        ];

        $department = Department::firstOrCreate(
            [
                'company_id' => 1,
                'department_code' => 2030
            ],
            [
                'status' => 'A',
                'description' => 'IT',
                'department_name' => 'IT Department'
            ]
        );

        foreach ($userAssignments as $username => $val) {
            $user = User::firstOrCreate(
                [
                    'user_name' => $username,
                    'email' => $username . '@gmail.com'
                ],
                [
                    'name'              => ucwords($val[1] . ' ' . $val[2]),
                    'email_verified_at' => now(),
                    'password'          => Hash::make('abiscus#!2030'),
                    'status'            => 'A',
                    'first_name'        => $val[1],
                    'last_name'         => $val[2],
                    'phone_number'      => $val[3],
                    'department_id'     => $department?->id,
                    'is_active'         => true,
                    'remember_token'    => Str::random(10),
                    'requires_password_reset' => true,
                    'failed_login_attempts' => 0,
                    'role_id'           => $adminRole?->id,
                    'last_login'        => now(),
                ]
            );

            if ($user && $adminRole) {
                $user->assignRole($adminRole->name);
                $user->syncPermissions($adminRole->permissions);
            }
        }
    }

    /**
     * Truncate permissions table with cascade and relationship handling
     *
     * @return void
     */
    public static function truncatePermissionsWithRelations()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;');
        try {
            DB::table('model_has_permissions')->truncate();
            DB::table('role_has_permissions')->truncate();

            DB::table('permissions')->truncate();
            DB::table('roles')->truncate();
            DB::table('users')->truncate();
            DB::table('company_department')->truncate();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            DB::statement('SET session_replication_role = origin;');
        }
    }

    /**
     * Truncate with additional cleanup
     *
     * @return void
     */
    public static function deepCleanPermissions()
    {
        DB::beginTransaction();
        try {
            DB::transaction(function () {
                DB::statement('
                    DELETE FROM model_has_permissions
                    WHERE permission_id NOT IN (SELECT id FROM permissions)
                ');

                // Truncate main process
                self::truncatePermissionsWithRelations();

                if (class_exists(PermissionRegistrar::class)) {
                    app(PermissionRegistrar::class)->forgetCachedPermissions();
                }
            });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
