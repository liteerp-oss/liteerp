import React, { useCallback, useEffect } from 'react'
import { useI18n } from '@/i18n/useI18n'
import { InputForm } from '../UI/Input/InputForm';
import PermissionService from '@/react/services/PermissionService';
export default function GroupPermissionForm({
    form = null}
) {
    const { t } = useI18n();
    return <div>
        <div className='mb-3'>
            <InputForm
                name='name'
                label={t('Name')}
                value={form.formData?.name}
                handleChange={form.handleChange}
                errorMessage={form.formErrors?.name}
            />
        </div>
    </div>
}       