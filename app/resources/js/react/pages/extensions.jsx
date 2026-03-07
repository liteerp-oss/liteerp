import React, { useCallback, useEffect, useRef, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import PageHead from '../components/PageHead'
import ExtensionService from '../services/ExtensionService';
import ExtensionCard from '../components/Extension/ExtensionCard';
import {usePopup} from '../components/popups/PopupContext'
import LoadingBox from '../components/LoadingBox'
import EmptyBox from '../components/Emptybox'
import { useI18n } from '../../i18n/useI18n';
import CreateButton from '../components/UI/PermissionButtons/CreateButton';
export default function Extensions() {
    const {t} = useI18n();
    let fileRef = useRef(null);
    const [loading,setLoading] = useState(false)
    const { openPopup } = usePopup();
    const [extensions, setExtensions] = useState([]);
    const list = useCallback(() => {
        setLoading(true)
        ExtensionService.list({})
            .then((resp) => {
                setExtensions(resp.message);
                setLoading(false)
            })
            .catch((error) => {
                setLoading(false)
                if(error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response?.data?.message
                    })
                }
            })
    }, []);
    const uploadExtension = useCallback(() => {
        let form = new FormData();
        form.append('file',fileRef.current.files[0]);
        ExtensionService.add(form)
        .then((resp) => {
            openPopup({
                type: 'success',
                message: t('You has been uploaded')
            })
            setExtensions([])
            list();
            
        })
        .catch((error) => {
            if(error.response?.data?.message) {
                openPopup({
                    type: 'error',
                    message: error.response?.data?.message
                })
            }
        })
    },[fileRef,list])
    const upload = () => {
        fileRef.current.click();
    }
    useEffect(() => {
        list()
    }, [])
    return <DashboardLayout>
        <div>
            <PageHead title={t('Extension')}
                subtitle={t('extension_page_desc')}
            />
            <div className=''>
                <div className='border-bottom'>
                    <div className='container'>
                        <div className='row mb-2 pt-3 pb-2 px-1'>
                            <div className='col-8'>
                                <div className='d-flex'>
                                    <h4 style={{
                                        marginRight: 20
                                    }}>{t('All extensions')}</h4>
                                    <CreateButton height={25} type={'extension'} onClick={upload} label={t('Upload')} />
                                        <input onChange={uploadExtension} ref={fileRef} type='file' id='file' style={{
                                            display: 'none'
                                        }} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className='container mt-3'>
                    <div className='row'>
                        {loading ?<LoadingBox/>: null }
                        {extensions?.map((item, index) => {
                            return <div className='col-4 mb-3' key={index}>
                                <ExtensionCard item={item} />
                            </div>
                        })}
                        {extensions?.length === 0 ? <EmptyBox message={t('Please install extensions')}/> : null}
                    </div>

                </div>
            </div>
        </div>
    </DashboardLayout>
}