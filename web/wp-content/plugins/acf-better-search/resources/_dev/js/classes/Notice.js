export default class Notice
{
  constructor()
  {
    if (!this.setVars()) return;

    this.setEvents();
  }

  setVars()
  {
    this.notice = document.querySelector('.notice[data-notice=acf-better-search]');
    if (!this.notice) return;

    this.settings = {
      isHidden: false,
      ajaxUrl: this.notice.getAttribute('data-url'),
    };

    return true;
  }

  setEvents()
  {
    window.addEventListener('load', this.getButtons.bind(this));
  }

  getButtons()
  {
    this.buttonClose       = this.notice.querySelector('.notice-dismiss');
    this.buttonPermanently = this.notice.querySelector('[data-permanently]');
    this.setButtonsEvents();
  }

  setButtonsEvents()
  {
    this.buttonClose.addEventListener('click', () => {
      this.hideNotice(false);
    });
    this.buttonPermanently.addEventListener('click', (e) => {
      e.preventDefault();
      this.hideNotice(true);
    });
  }

  hideNotice(isPermanently)
  {
    if (this.settings.isHidden) return;
    this.settings.isHidden = true;

    jQuery.ajax(this.settings.ajaxUrl, {
      type: 'POST',
      data: {
        is_permanently: isPermanently ? 1 : 0,
      },
    });
    this.buttonClose.click();
  }
}
