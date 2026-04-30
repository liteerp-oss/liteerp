import React from 'react'
import LanguageSwitcher from './LanguageSwitcher'
export default function Footer() {
    return <footer className="border-top theme-bg">
        <div className="container-fluid py-2">
            <div className="row align-items-center custom-text-muted small">
                <div className="col-md-4 d-flex align-items-center gap-2 justify-content-center justify-content-md-start">
                    <div className='d-flex'>
                        <span>LiteERP © 2026</span>
                    </div>
                    <div>
                        <LanguageSwitcher/>
                    </div>
                </div>
                <div className="col-md-8 d-flex align-items-center gap-3 justify-content-center justify-content-md-end">
                    <a href="https://www.hetzner.com" target="_blank"
                        className="custom-text-muted text-decoration-none d-flex align-items-center gap-1 hover-opacity">
                        <img src="/Hetzner-Logo.png" height={25} />
                        Infrastructure powered by Hetzner
                    </a>
                    <a href="https://github.com/liteerp-oss/liteerp" target="_blank"
                        className="custom-text-muted text-decoration-none d-flex align-items-center gap-1 hover-opacity">
                        <i className="bi bi-github"></i>
                        Github
                    </a>

                    <a href="https://github.com/liteerp-oss/docs"
                        className="custom-text-muted text-decoration-none d-flex align-items-center gap-1">
                        <i className="bi bi-book"></i>
                        Docs
                    </a>

                    <a href="https://github.com/liteerp-oss/liteerp/issues"
                        className="custom-text-muted text-decoration-none d-flex align-items-center gap-1">
                        <i className="bi bi-life-preserver"></i>
                        Support
                    </a>
                </div>

            </div>
        </div>
    </footer>
}