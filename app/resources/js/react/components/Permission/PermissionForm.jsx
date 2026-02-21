import React, { useCallback, useEffect } from 'react'
import { useI18n } from '@/i18n/useI18n'
import { InputForm } from '../UI/Input/InputForm';
import PermissionService from '@/react/services/PermissionService';
export default function PermissionForm({
    form = null,
    saveResponse = null}
) {
    const [permissionData, setPermissionData] = React.useState([]);
    useEffect(() => {
        if(form.formData?.id) {
            PermissionService.show(form.formData).then(res => {
            setPermissionData(res.message?.permissions || []);
        });
        }
    }, [form.formData?.id]);
    useEffect(() => {
        form.handleChangeByKey('permissions', permissionData);
    }, [permissionData]);
    const { t } = useI18n();
    return <div>
        <div className='mb-3'>
            {saveResponse?.permissions ? Object.keys(saveResponse?.permissions)?.map((feature) => {
                return <div key={feature} className='mb-3'>
                    <div className='mb-1 text-uppercase'>{t(feature)}</div>
                    <div className='d-flex flex-wrap gap-2'>
                        {saveResponse?.permissions[feature]?.map((action) => {
                            return <div key={action} className='d-flex align-items-center gap-1 mt-2'>
                                <input
                                    type="checkbox"
                                    name={`permissions[${feature}][]`}
                                    value={action}
                                    checked={permissionData.includes(action) ? true : false}
                                    onChange={(e) => {
                                        const checked = e.target.checked;
                                        const value = e.target.value;
                                        if (checked) {
                                            setPermissionData([...permissionData, action]);
                                        } else {
                                            const roles = permissionData.filter((item) => item !== value);
                                            setPermissionData(roles);
                                        }
                                    }}
                                />
                                <span>{t(action.split('.')[action.split('.').length - 1])}</span>
                            </div>
                        })}
                    </div>
                </div>
            }) : null}
        </div>
    </div>
}       