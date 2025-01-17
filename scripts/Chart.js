document.addEventListener("DOMContentLoaded", async () => {
  const fetchData = async () => {
    try {
      const response = await fetch(
        "https://swadeshchhetri.github.io/FinanceManagementPortal/fetch_database.json"
      );
      if (!response.ok) throw new Error("Failed to fetch data");
      return await response.json();
    } catch (error) {
      console.error("Error fetching data:", error);
      return null;
    }
  };

  const data = await fetchData();
  if (!data) return;

  // ---- Render Cards ----
  const totalExpenseCard = document.getElementById("total-expense");
  const totalIncomeCard = document.getElementById("total-income");
  const bankBalanceCard = document.getElementById("bank-balance");
  const totalInvestmentCard = document.getElementById("total-investment");

  // Calculate Total Expense
  const totalExpense = data.daily_report.reduce((sum, item) => {
    return sum + parseFloat(item.expense || 0);
  }, 0);
  totalExpenseCard.innerText = `₹ ${totalExpense.toFixed(2)}`;

  // Calculate Total Income
  const totalIncome = data.daily_report.reduce((sum, item) => {
    return sum + parseFloat(item.income || 0);
  }, 0);
  totalIncomeCard.innerText = `₹ ${totalIncome.toFixed(2)}`;

  // Calculate Bank Balance (Assumed from daily report or other data)
  const bankBalance = totalIncome - totalExpense;
  bankBalanceCard.innerText = `₹ ${bankBalance.toFixed(2)}`;

  const totalInvestment = data.total_investment;
  totalInvestmentCard.innerHTML = totalInvestment;

  // Chart 1: Daily, Weekly, Monthly, Quarterly, Yearly
  const updateChart = (chart, labels, incomeData, expenseData) => {
    chart.data.labels = labels;
    chart.data.datasets[0].data = incomeData;
    chart.data.datasets[1].data = expenseData;
    chart.update();
  };

  const combinedChart = new Chart(document.getElementById("combinedChart"), {
    type: "bar",
    data: {
      labels: [],
      datasets: [
        { label: "Income", data: [], backgroundColor: "#4B49AC" },
        { label: "Expense", data: [], backgroundColor: "#7978E9" },
      ],
    },
    options: { responsive: true, plugins: { legend: { position: "top" } } },
  });

  document.getElementById("periodSelector").addEventListener("change", (e) => {
    const period = e.target.value;
    let labels, incomeData, expenseData;

    switch (period) {
      case "daily":
        labels = data.daily_report.map((item) => item.day);
        incomeData = data.daily_report.map((item) => item.income);
        expenseData = data.daily_report.map((item) => item.expense);
        break;
      case "weekly":
        labels = data.weekly_report.map(
          (item) => `Week ${item.week}, ${item.year}`
        );
        incomeData = data.weekly_report.map((item) => item.income);
        expenseData = data.weekly_report.map((item) => item.expense);
        break;
      case "monthly":
        labels = data.monthly_report.map((item) => item.month);
        incomeData = data.monthly_report.map((item) => item.income);
        expenseData = data.monthly_report.map((item) => item.expense);
        break;

      case "quarterly":
        labels = data.quarterly_report.map((item) => item.quarter);
        incomeData = data.quarterly_report.map((item) => item.income);
        expenseData = data.quarterly_report.map((item) => item.expense);
        break;

      case "yearly":
        labels = data.yearly_report.map((item) => item.year);
        incomeData = data.yearly_report.map((item) => item.income);
        expenseData = data.yearly_report.map((item) => item.expense);
        break;
    }

    updateChart(combinedChart, labels, incomeData, expenseData);
  });

  document.getElementById("periodSelector").value = "daily";
  document.getElementById("periodSelector").dispatchEvent(new Event("change"));

  // Chart 2: Monthly Income & Expense chart
  const ctx2 = document.getElementById("expenseIncomeChart").getContext("2d");
  const datalabels = data.monthly_report.map((item) => item.month);
  const incomeData = data.monthly_report.map((item) => item.income);
  const expenseData = data.monthly_report.map((item) => item.expense);
  new Chart(ctx2, {
    type: "bar",
    data: {
      labels: datalabels, // Months from fetched data
      datasets: [
        {
          label: "Income",
          data: incomeData,
          backgroundColor: "rgba(75, 73, 172, 0.8)",
          borderColor: "rgba(75, 73, 172, 1)",
          borderWidth: 1,
        },
        {
          label: "Expense",
          data: expenseData,
          backgroundColor: "#7978E9",
          borderColor: "rgba(255, 99, 132, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
      },
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });

  // Chart 3: Expense-wise transaction chart
  const ctx3 = document.getElementById("transactionChart").getContext("2d");
  new Chart(ctx3, {
    type: "pie",
    data: {
      labels: data.categories.map((item) => item.label), // Categories
      datasets: [
        {
          data: data.categories.map((item) => item.value), // Values
          backgroundColor: [
            "rgba(75, 73, 172, 0.7)", // #4B49AC
            "rgba(114, 96, 228, 0.7)", // Lighter purple
            "rgba(192, 116, 248, 0.7)", // Pastel violet
            "rgba(255, 153, 204, 0.7)", // Soft pink
            "rgba(102, 153, 255, 0.7)", // Soft blue
          ],
          borderColor: [
            "rgba(75, 73, 172, 1)", // #4B49AC
            "rgba(114, 96, 228, 1)", // Matches lighter purple
            "rgba(192, 116, 248, 1)", // Matches pastel violet
            "rgba(255, 153, 204, 1)", // Matches soft pink
            "rgba(102, 153, 255, 1)", // Matches soft blue
          ],
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "right",
        },
      },
    },
  });

  // Chart 4: Active Project Status
  const ctx4 = document.getElementById("statusBudgetChart").getContext("2d");

  const activeProjectsBudget =
    data.active_projects.find((item) => item.status === "active")
      ?.total_budget || 0;
  const completedProjectsBudget =
    data.active_projects.find((item) => item.status === "completed")
      ?.total_budget || 0;
  const onHoldProjectsBudget =
    data.active_projects.find((item) => item.status === "inactive")
      ?.total_budget || 0;

  const activeBudget = parseFloat(activeProjectsBudget);
  const completedBudget = parseFloat(completedProjectsBudget);
  const onHoldBudget = parseFloat(onHoldProjectsBudget);

  const groupedBarChart = new Chart(ctx4, {
    type: "bar",
    data: {
      labels: ["Budget Allocation"],
      datasets: [
        {
          label: "Active",
          data: [activeBudget],
          backgroundColor: "rgba(75, 73, 172, 0.8)",
          borderColor: "rgba(75, 73, 172, 1)",
          borderWidth: 1,
        },
        {
          label: "Completed",
          data: [completedBudget],
          backgroundColor: "rgba(114, 96, 228, 0.8)",
          borderColor: "rgba(114, 96, 228, 1)",
          borderWidth: 1,
        },
        {
          label: "In-Active",
          data: [onHoldBudget],
          backgroundColor: "rgba(192, 116, 248, 0.8)",
          borderColor: "rgba(192, 116, 248, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
        tooltip: {
          mode: "index",
          intersect: false,
        },
      },
      scales: {
        x: {
          beginAtZero: true,
        },
        y: {
          beginAtZero: true,
        },
      },
    },
  });

  // Chart 5: Financial Growth Area Chart
  const ctx5 = document.getElementById("growthChart").getContext("2d");

  const growthlabels = data.financial_growth.map((item) => item.month);
  const growthvalues = data.financial_growth.map((item) =>
    parseFloat(item.growth)
  );

  // Create the new chart
  new Chart(ctx5, {
    type: "line",
    data: {
      labels: growthlabels,
      datasets: [
        {
          label: "Financial Growth",
          data: growthvalues,
          backgroundColor: "rgba(75, 73, 172, 0.8)",
          borderColor: "rgba(75, 73, 172, 1)",
          borderWidth: 2,
          tension: 0.4,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true, // Start the Y-axis from 0
        },
      },
      plugins: {
        legend: {
          position: "top", // Position the legend at the top
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              return `${context.dataset.label}: ${context.raw.toFixed(2)}`;
            },
          },
        },
      },
    },
  });
});
