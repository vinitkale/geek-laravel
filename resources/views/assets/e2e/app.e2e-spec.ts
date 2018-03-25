import { GeekmeetPage } from './app.po';

describe('geekmeet App', function() {
  let page: GeekmeetPage;

  beforeEach(() => {
    page = new GeekmeetPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
