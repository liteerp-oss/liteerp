import React from 'react'
export default function Logo() {
    return <img src={import.meta.env.VITE_LOGO_PATH + "/logo-full.png"} alt='' style={{
          maxWidth: '100%',
          height: 60
        }} />
}