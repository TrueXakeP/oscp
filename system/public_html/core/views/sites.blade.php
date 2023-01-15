@php /** @var \OpenServer\DTO\Domain[] $group */ @endphp

<div class="row sites">
  <div class="col-md-3 site-groups">
    <nav class="nav flex-column">
      @foreach ($sites as $groupName => $group)
        <a class="text-end nav-link" href="#"
          data-bs-toggle="tab"
          data-bs-target="#tab-site-{{ str_replace('.', '-', $groupName) }}"
        >{{ $groupName !== TLD ? '.' : '' }}{{ $groupName }}</a>
      @endforeach
    </nav>
  </div>
  <div class="col-md-9">
    <div class="tab-content">
      @foreach ($sites as $groupName => $group)
        <div class="domains tab-pane fade" id="tab-site-{{ str_replace('.', '-', $groupName) }}">
          <table class="table table-borderless">
            @foreach ($group as $domain)
              <tr>
                <td class="p-0">
                  <div class="d-flex align-items-center">
                    @if (!$domain->isAvailable())
                      <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-2"
                        title="Модуль {{ $domain->engine }} отсутствует или выключен"
                      ></i>
                    @elseif(!$domain->isValidRoot())
                      <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-2"
                        title="Неверная папка домена"
                      ></i>
                    @else
                      @if($domain->admin_path)
                        <a href="http://{{ $domain->host }}{{ $domain->admin_path }}" target="_blank">
                          <i class="bi bi-box-arrow-in-right fs-4 text-danger me-2"></i>
                        </a>
                      @else
                        <i class="bi bi-box-arrow-in-right fs-4 text-light me-2"></i>
                      @endif
                    @endif
                    @if($domain->isValidRoot())
                      <a href="http://{{ $domain->host }}" target="_blank">http://{{ $domain->host }}</a>
                    @else
                      http://{{ $domain->host }}
                    @endif
                  </div>
                </td>
                <td class="p-0">
                  @if (!$domain->isValidRoot())
                    <span class="text-danger">Неверная папка домена</span>
                  @elseif ($domain->ssl)
                    <div class="d-flex align-items-center">
                      @if($domain->admin_path)
                        <a href="https://{{ $domain->host }}{{ $domain->admin_path }}" target="_blank">
                          <i class="bi bi-box-arrow-in-right fs-4 text-danger me-2"></i>
                        </a>
                      @else
                        <i class="bi bi-box-arrow-in-right fs-4 text-light me-2"></i>
                      @endif
                      <a href="https://{{ $domain->host }}" target="_blank">https://{{ $domain->host }}</a>
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      @endforeach
    </div>
  </div>
</div>
