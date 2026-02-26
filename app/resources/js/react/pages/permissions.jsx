import React, { useEffect } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import PageHead from '../components/PageHead'
import CommonDataTableV2 from '../components/CommonDataTableV2'
import { useForm } from '../libraries/handleInput'
import { useI18n } from '@/i18n/useI18n'
import useTable from '../libraries/handleTable'
import PermissionGroupService from '../services/PermissionGroupService'
import StatusBadge from '../components/StatusBadge'
import { PopupLayout } from '../layouts/PopupLayout'
import PermissionForm from '../components/Permission/PermissionForm'
import PermissionService from '../services/PermissionService'
import { usePopup } from '../components/popups/PopupContext'
import GroupPermissionForm from '../components/Permission/GroupPermissionForm'
export default function Permissions(){
    const {openPopup} = usePopup();
    const [saveResponse,setSaveResponse] = React.useState(null);    
    const [popupEdit, setPopupEdit] = React.useState(false);
    const [popupAdd, setPopupAdd] = React.useState(false);
    const [popupAddPermission, setPopupAddPermission] = React.useState(false);
    const search = useForm();
    const { t, lang } = useI18n();
    const form = useForm();
    const table = useTable();
    const getList = () => {
        PermissionGroupService.list({
            ...search.formData
        }).then(res => {
            table.setData(res.message.data);
        });
        PermissionService.view().then(res => {
            setSaveResponse(res.message);
        });
    }
    useEffect(() => {
        table.setColums([{
            key: "id",
            label: t("ID"),
        },{
            key: "name",
            label: t("Name"),
        },{
            key: "type",
            label: t("Type"),
            render: (type) => {
                if(type === "admin"){
                    return <StatusBadge status={type}/>
                }else{
                    return <StatusBadge status={'default'}/>
                }
            }
        },{
            key: "created_by_name",
            label: t("Created By"),
        }])
        getList();
    }, [lang]);
    const onEdit = (data) => {
        setPopupEdit(true);
        form.setFormData(data);
    }
    const saveGroup = () => {
        form.setLoading(true)
        PermissionGroupService.store({
            ...form.formData
        }).then(res => {
            form.setLoading(false)
            openPopup({
                    type: 'success',
                    message: t('Permission group has been created successfully!'),
                });
            setPopupAdd(false);
            getList();
        }).catch((error) => {
            if(error.response?.data?.message){
                form.setFormErrors(error.response.data.message);
                openPopup({
                    type: 'error',
                    message: error.response.data.message
                });
            }
        });
    }
    const editGroup = () => {
        form.setLoading(true)
        PermissionGroupService.update({
            ...form.formData
        }).then(res => {
            form.setLoading(false)
            openPopup({
                    type: 'success',
                    message: t('Permission group has been updated successfully!'),
                });
            setPopupEdit(false);
            getList();
        }).catch((error) => {
            if(error.response?.data?.message){
                form.setFormErrors(error.response.data.message);
                openPopup({
                    type: 'error',
                    message: error.response.data.message
                });
            }
        });
    }
    const onPermission = (data) => {
        setPopupAddPermission(true);
        form.setFormData(data);
    }
    const savePermission = () => {
        form.setLoading(true)
        PermissionService.store({
            ...form.formData,
            group_id: form.formData?.id 
        }).then(res => {
            form.setLoading(false);
            openPopup({
                    type: 'success',
                    message: t('Permission has been added successfully!'),
                });
            setPopupAddPermission(false);
            getList();
        }).catch((error) => {
            form.setLoading(false);
            if(error.response?.data?.message){
                form.setFormErrors(error.response.data.message);
                openPopup({
                    type: 'error',
                    message: error.response.data.message
                });
            }
        });
    }
    const onDelete = (data) => {
        openPopup({
                    type: 'warning',
                    message: t('Are you sure you want to delete this permission group?'),
                    onConfirm: () => {
                        PermissionGroupService.delete(data).then(res => {
                            openPopup({
                                type: 'success',
                                message: t('Permission group has been deleted successfully!'),
                            });
                            getList();
                        }).catch((error) => {
                            if(error.response?.data?.message){
                                openPopup({
                                    type: 'error',
                                    message: error.response.data.message
                                });
                            }
                        });
                    }
                });
    }
    return <DashboardLayout>
        <div>
            <PageHead
            title={t('Permissions')}
            subtitle={t('permission_desc')}
            />
            <div className='container mt-3'>
                <CommonDataTableV2
                add={() => {
                    setPopupAdd(true);
                    form.setFormData(null)
                }}
                type={'permissiongroup'}
                columns={table.colums}
                data={table.data}
                links={table.links}
                search={search}
                onEdit={onEdit}
                onShow={onPermission}
                onDelete={onDelete}
                callback={getList}
                />
            </div>
            {popupEdit && <PopupLayout 
            onClose={() => setPopupEdit(false)}
            confirmText={t("Save changes")}
            onConfirm={() => editGroup()}
            title={t("Edit Permission Group")}>
                <div>
                    <GroupPermissionForm form={form} saveResponse={saveResponse}/>
                </div>
            </PopupLayout>}
            {popupAdd && <PopupLayout 
            onClose={() => setPopupAdd(false)}
            onConfirm={() => saveGroup()}
            title={t("Add Permission Group")}>
                <div>
                    <GroupPermissionForm form={form} saveResponse={saveResponse}/>
                </div>
            </PopupLayout>}
            {popupAddPermission && <PopupLayout 
            onClose={() => setPopupAddPermission(false)}
            onConfirm={() => savePermission()}
            confirmText={t('Save changes')}
            title={t("Add Permission")}>
                <div>
                    <PermissionForm form={form} saveResponse={saveResponse}/>
                </div>
            </PopupLayout>}
        </div>
    </DashboardLayout>
}